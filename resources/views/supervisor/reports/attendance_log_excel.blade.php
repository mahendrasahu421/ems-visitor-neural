<table>
    <thead>
        <tr>
            <th colspan="11" style="font-size: 16px; font-weight: bold; text-align: center; height: 35px; vertical-align: middle; background-color: #0f766e; color: #ffffff;">
                SUPERVISOR TEAM ATTENDANCE PUNCH LOGS REPORT
            </th>
        </tr>
        <tr>
            <th colspan="11" style="font-size: 11px; text-align: center; color: #475569; height: 25px; vertical-align: middle; background-color: #f1f5f9;">
                Supervisor: {{ $supervisor->employee_name }} (ID: {{ $supervisor->employee_id }}) &nbsp;|&nbsp; Period: {{ $periodText }} &nbsp;|&nbsp; Generated: {{ now()->format('d M, Y h:i A') }}
            </th>
        </tr>
        <tr>
            <th colspan="11" style="height: 10px;"></th>
        </tr>
        <tr>
            <th colspan="11" style="font-size: 12px; font-weight: bold; background-color: #e2e8f0; border: 1px solid #94a3b8; text-align: left;">
                SUMMARY: Total Records: {{ $summary['total_records'] }} &nbsp;|&nbsp; Total Hours Worked: {{ $summary['total_hours_worked'] }} hrs &nbsp;|&nbsp; Late Arrivals: {{ $summary['late_arrivals'] }} &nbsp;|&nbsp; Early Arrivals: {{ $summary['early_arrivals'] }}
            </th>
        </tr>
        <tr>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: center; width: 35px;">#</th>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: left; min-width: 160px;">Employee Name</th>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: left; min-width: 120px;">Designation</th>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: left; min-width: 120px;">Agency</th>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: center; min-width: 95px;">Date</th>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: center; min-width: 75px;">Day</th>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: center; min-width: 90px;">Check In</th>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: center; min-width: 90px;">Check Out</th>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: center; min-width: 95px;">Worked Hours</th>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: center; min-width: 95px;">Status</th>
            <th style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8; text-align: left; min-width: 180px;">Punctuality / Notes</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($records as $index => $rec)
            @php
                $statusBg = match($rec['status']) {
                    'PRESENT'   => 'background-color: #dcfce7; color: #15803d;',
                    'CHECKED_IN'=> 'background-color: #e0f2fe; color: #0369a1;',
                    'LATE'      => 'background-color: #fef3c7; color: #92400e;',
                    default     => 'background-color: #f8fafc; color: #475569;',
                };
            @endphp
            <tr>
                <td style="border: 1px solid #cbd5e1; text-align: center; vertical-align: middle;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #cbd5e1; font-weight: bold; vertical-align: middle;">{{ $rec['employee']['name'] }}</td>
                <td style="border: 1px solid #cbd5e1; vertical-align: middle;">{{ $rec['employee']['designation'] }}</td>
                <td style="border: 1px solid #cbd5e1; vertical-align: middle;">{{ $rec['employee']['agency'] }}</td>
                <td style="border: 1px solid #cbd5e1; text-align: center; vertical-align: middle;">{{ $rec['date'] }}</td>
                <td style="border: 1px solid #cbd5e1; text-align: center; vertical-align: middle;">{{ $rec['day'] }}</td>
                <td style="border: 1px solid #cbd5e1; text-align: center; vertical-align: middle; font-weight: bold; color: #0284c7;">{{ $rec['check_in_time'] ?: '-' }}</td>
                <td style="border: 1px solid #cbd5e1; text-align: center; vertical-align: middle; font-weight: bold; color: #475569;">{{ $rec['check_out_time'] ?: '-' }}</td>
                <td style="border: 1px solid #cbd5e1; text-align: center; vertical-align: middle; font-weight: bold;">{{ $rec['working_formatted'] }}</td>
                <td style="border: 1px solid #cbd5e1; text-align: center; vertical-align: middle; font-weight: bold; {{ $statusBg }}">{{ $rec['status'] }}</td>
                <td style="border: 1px solid #cbd5e1; vertical-align: middle;">
                    @if($rec['punctuality']['is_late'])
                        <span style="color: #dc2626; font-weight: bold;">Late by {{ $rec['punctuality']['late_formatted'] }}</span>
                    @elseif($rec['punctuality']['is_early_in'])
                        <span style="color: #16a34a;">Early by {{ $rec['punctuality']['early_in_minutes'] }} mins</span>
                    @else
                        On Time
                    @endif
                    @if(!empty($rec['notes']))
                        ({{ $rec['notes'] }})
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="11" style="text-align: center; padding: 15px; color: #64748b; border: 1px solid #cbd5e1;">No attendance records found for the selected filter.</td>
            </tr>
        @endforelse
    </tbody>
</table>
