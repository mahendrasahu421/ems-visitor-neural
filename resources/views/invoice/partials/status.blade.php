@php
    $statusMap = [
        0 => ['Draft', 'secondary'],
        1 => ['Pending', 'warning'],
        2 => ['Paid', 'success'],
        3 => ['Cancelled', 'danger'],
    ];
    [$label, $color] = $statusMap[(int) ($status ?? 0)] ?? ['Unknown', 'secondary'];
@endphp

<span class="badge bg-{{ $color }}">{{ $label }}</span>
