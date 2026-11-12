<table>
    <thead>
        <tr>
            <th colspan="10" style="font-size: 16px; font-weight: bold; text-align: center; height: 35px; vertical-align: middle; background-color: #1e3a8a; color: #ffffff;">
                SUPERVISOR TEAM LEAVE REPORT
            </th>
        </tr>
        <tr>
            <th colspan="10" style="font-size: 11px; text-align: center; color: #475569; height: 25px; vertical-align: middle; background-color: #f1f5f9;">
                Supervisor: {{ $supervisor->employee_name }} (ID: {{ $supervisor->employee_id }}) &nbsp;|&nbsp; Period: {{ $periodText }} &nbsp;|&nbsp; Generated: {{ now()->format('d M, Y h:i A') }}
            </th>
        </tr>
        <tr>
            <th colspan="10" style="height: 10px;"></th>
        </tr>
        <tr>
            <th colspan="10" style="font-size: 12px; font-weight: bold; background-color: #e2e8f0; border: 1px solid #94a3b8; text-align: left;">
                LEAVE APPLICATIONS (Total: {{ count($leaves) }} | Approved: {{ $summary['approved_count'] }} ({{ $summary['approved_total_days'] }} Days) | Pending: {{ $summary['pending_count'] }} | Rejected: {{ $summary['rejected_count'] }})
            </th>
        </tr>
        <tr>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: center; width: 35px;">#</th>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: left; min-width: 160px;">Employee Name</th>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: left; min-width: 120px;">Designation</th>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: left; min-width: 120px;">Agency</th>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: left; min-width: 130px;">Leave Type</th>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: center; min-width: 100px;">From Date</th>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: center; min-width: 100px;">To Date</th>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: center; width: 60px;">Days</th>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: center; min-width: 100px;">Status</th>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: left; min-width: 250px;">Reason</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($leaves as $index => $leave)
            @php
                $statusColor = match($leave['status']) {
                    'APPROVED' => 'background-color: #dcfce7; color: #15803d;',
                    'REJECTED' => 'background-color: #fee2e2; color: #b91c1c;',
                    default    => 'background-color: #fef3c7; color: #92400e;',
                };
            @endphp
            <tr>
                <td style="border: 1px solid #cbd5e1; text-align: center; vertical-align: middle;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #cbd5e1; font-weight: bold; vertical-align: middle;">{{ $leave['employee_name'] }}</td>
                <td style="border: 1px solid #cbd5e1; vertical-align: middle;">{{ $leave['designation'] }}</td>
                <td style="border: 1px solid #cbd5e1; vertical-align: middle;">{{ $leave['agency'] }}</td>
                <td style="border: 1px solid #cbd5e1; vertical-align: middle;">{{ $leave['leave_type'] }}</td>
                <td style="border: 1px solid #cbd5e1; text-align: center; vertical-align: middle;">{{ $leave['from_date'] }}</td>
                <td style="border: 1px solid #cbd5e1; text-align: center; vertical-align: middle;">{{ $leave['to_date'] }}</td>
                <td style="border: 1px solid #cbd5e1; text-align: center; vertical-align: middle; font-weight: bold;">{{ $leave['duration'] }}</td>
                <td style="border: 1px solid #cbd5e1; text-align: center; vertical-align: middle; font-weight: bold; {{ $statusColor }}">{{ $leave['status'] }}</td>
                <td style="border: 1px solid #cbd5e1; vertical-align: middle;">{{ $leave['reason'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="10" style="text-align: center; padding: 15px; color: #64748b; border: 1px solid #cbd5e1;">No leave records found for the selected period.</td>
            </tr>
        @endforelse

        {{-- Spacing --}}
        <tr>
            <th colspan="10" style="height: 20px;"></th>
        </tr>

        {{-- Balances Table --}}
        <tr>
            <th colspan="10" style="font-size: 13px; font-weight: bold; background-color: #1e293b; color: #ffffff; text-align: left; height: 28px; vertical-align: middle;">
                TEAM LEAVE BALANCES SUMMARY (Year: {{ $year }})
            </th>
        </tr>
        <tr>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: center;">#</th>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: left;" colspan="2">Employee Name</th>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: left;" colspan="2">Designation</th>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: left;">Leave Type</th>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: center;">Allotted</th>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: center;">Used</th>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: center;">Pending</th>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: center;">Remaining</th>
        </tr>
        @php $bIdx = 1; @endphp
        @foreach ($member_balances as $mb)
            @foreach ($mb['balances'] as $bal)
                <tr>
                    <td style="border: 1px solid #cbd5e1; text-align: center;">{{ $bIdx++ }}</td>
                    <td style="border: 1px solid #cbd5e1; font-weight: bold;" colspan="2">{{ $mb['employee_name'] }}</td>
                    <td style="border: 1px solid #cbd5e1;" colspan="2">{{ $mb['designation'] }}</td>
                    <td style="border: 1px solid #cbd5e1;">{{ $bal['leave_name'] ?? $bal['leave_type_name'] ?? 'Leave' }}</td>
                    <td style="border: 1px solid #cbd5e1; text-align: center;">{{ $bal['allotted_days'] }}</td>
                    <td style="border: 1px solid #cbd5e1; text-align: center; color: #dc2626;">{{ $bal['used_days'] }}</td>
                    <td style="border: 1px solid #cbd5e1; text-align: center; color: #d97706;">{{ $bal['pending_days'] }}</td>
                    <td style="border: 1px solid #cbd5e1; text-align: center; font-weight: bold; color: #16a34a;">{{ $bal['remaining_days'] }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
