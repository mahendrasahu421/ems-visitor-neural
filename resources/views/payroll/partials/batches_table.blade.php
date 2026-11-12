<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light fs-12 text-uppercase text-secondary letter-spacing">
            <tr>
                <th class="ps-3" style="width: 50px;">#</th>
                <th>Batch Number</th>
                <th>Payroll Period</th>
                <th>Employees</th>
                <th>Total Gross</th>
                <th>Total Deductions</th>
                <th>Net Disbursed</th>
                <th>Status</th>
                <th>Payment Info</th>
                <th class="text-end pe-3" style="width: 140px;">Actions</th>
            </tr>
        </thead>
        <tbody class="fs-13">
            @forelse($batches as $index => $batch)
                <tr>
                    <td class="ps-3 text-muted fs-12 fw-semibold">
                        {{ $batches->firstItem() + $index }}
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('payroll.batches.show', $batch->id) }}" class="fw-bold text-primary text-decoration-none fs-13">
                                {{ $batch->batch_number }}
                            </a>
                            <button type="button" class="btn btn-sm btn-icon btn-ghost-secondary p-0" title="Copy Batch Number"
                                onclick="navigator.clipboard.writeText('{{ $batch->batch_number }}'); this.innerHTML='<i class=\'ti ti-check text-success fs-13\'></i>'; setTimeout(() => this.innerHTML='<i class=\'ti ti-copy fs-13\'></i>', 1500);">
                                <i class="ti ti-copy fs-13"></i>
                            </button>
                        </div>
                        <div class="text-muted fs-11 mt-0.5 d-flex align-items-center gap-1">
                            <i class="ti ti-clock fs-11"></i>
                            <span>{{ $batch->created_at ? $batch->created_at->diffForHumans() : 'Recently' }}</span>
                            @if($batch->notes)
                                <span class="text-truncate d-inline-block ms-1" style="max-width: 180px;" title="{{ $batch->notes }}">
                                    &bull; {{ $batch->notes }}
                                </span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-1.5">
                            <span class="badge bg-light text-dark border p-1 rounded">
                                <i class="ti ti-calendar fs-13 text-primary"></i>
                            </span>
                            <span class="fw-semibold text-dark fs-13">
                                {{ \Carbon\Carbon::createFromDate($batch->year, $batch->month, 1)->format('F Y') }}
                            </span>
                        </div>
                        <span class="fs-11 text-muted">Cycle: {{ sprintf('%02d/%d', $batch->month, $batch->year) }}</span>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border px-2 py-1 fs-12">
                            <i class="ti ti-users me-1 text-primary"></i> {{ $batch->total_employees }} Staff
                        </span>
                    </td>
                    <td>
                        <span class="fw-semibold text-dark fs-13">
                            &#8377; {{ number_format($batch->total_gross, 2) }}
                        </span>
                    </td>
                    <td>
                        <span class="text-danger fw-semibold fs-13">
                            &#8377; {{ number_format($batch->total_deductions, 2) }}
                        </span>
                    </td>
                    <td>
                        <span class="fw-bold text-success fs-14">
                            &#8377; {{ number_format($batch->total_net_pay, 2) }}
                        </span>
                    </td>
                    <td>
                        @if($batch->status === 'Paid')
                            <span class="badge bg-success-subtle text-success border border-success-subtle fs-11 px-2.5 py-1">
                                <i class="ti ti-check me-1"></i> Paid
                            </span>
                        @elseif($batch->status === 'Approved')
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11 px-2.5 py-1">
                                <i class="ti ti-thumb-up me-1"></i> Approved
                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11 px-2.5 py-1">
                                <i class="ti ti-clock me-1"></i> Generated
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($batch->payment_date)
                            <div class="fs-12 text-dark font-weight-bold">
                                {{ \Carbon\Carbon::parse($batch->payment_date)->format('d M, Y') }}
                            </div>
                            <span class="fs-11 text-muted">{{ $batch->payment_mode ?? 'Bank Transfer' }}</span>
                        @else
                            <span class="badge bg-light text-muted border fs-11">Unpaid</span>
                        @endif
                    </td>
                    <td class="text-end pe-3">
                        <div class="d-inline-flex gap-1">
                            <a href="{{ route('payroll.batches.show', $batch->id) }}"
                                class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 shadow-sm px-2.5 py-1"
                                title="Open 31-Day Attendance Matrix & Review">
                                <i class="ti ti-layout-grid fs-13"></i>
                                <span>Review Grid</span>
                            </a>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border btn-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ti ti-dots-vertical fs-13"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm fs-12">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('payroll.batches.show', $batch->id) }}">
                                            <i class="ti ti-layout-grid me-1.5 text-primary"></i> Attendance Register Grid
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('payroll.payslips', ['month' => $batch->month, 'year' => $batch->year]) }}">
                                            <i class="ti ti-file-text me-1.5 text-info"></i> View Batch Payslips
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center py-5 text-muted">
                        <div class="avatar avatar-xl bg-light text-muted rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <i class="ti ti-search-off fs-26"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">No Payroll Batches Found</h6>
                        <p class="fs-13 text-muted mb-3">No records matched your selected search criteria or filters.</p>
                        <a href="{{ route('payroll.process') }}" class="btn btn-sm btn-primary">
                            <i class="ti ti-plus me-1"></i> Process New Batch
                        </a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination & Summary Footer -->
<div class="card-footer bg-white border-top py-3 px-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
    <div class="text-muted fs-12">
        @if($batches->total() > 0)
            Showing <strong class="text-dark">{{ $batches->firstItem() }}</strong> to
            <strong class="text-dark">{{ $batches->lastItem() }}</strong> of
            <strong class="text-dark">{{ $batches->total() }}</strong> payroll batches
        @else
            No batches to display
        @endif
    </div>
    <div class="pagination-container">
        {{ $batches->links() }}
    </div>
</div>
