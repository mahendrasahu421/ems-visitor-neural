<div class="border-top pt-4 mt-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="mb-0">Response Timeline</h6>
        <span class="text-muted small">Total work time: {{ $ticket->formattedTotalWorkTime() }}</span>
    </div>

    @forelse ($ticket->replies as $reply)
        <div class="border rounded p-3 mb-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                <div>
                    <strong>{{ $reply->employee?->employee_name ?? 'Employee' }}</strong>
                    <span class="badge bg-info ms-2">{{ $reply->status ?? 'Updated' }}</span>
                </div>
                <div class="text-muted small">
                    {{ $reply->created_at->format('d M Y, h:i A') }}
                </div>
            </div>
            <div class="text-muted small mb-2">Work time: {{ $reply->formattedWorkTime() }}</div>
            @if ($reply->note)
                <p class="mb-0">{{ $reply->note }}</p>
            @else
                <p class="mb-0 text-muted">No note added.</p>
            @endif
        </div>
    @empty
        <div class="alert alert-light border mb-0">No employee responses yet.</div>
    @endforelse
</div>
