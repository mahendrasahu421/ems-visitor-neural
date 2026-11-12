<table>
    <thead>
        <tr>
            <th colspan="{{ count($dates) + 6 }}" style="font-size: 16px; font-weight: bold; text-align: center; height: 35px; vertical-align: middle; background-color: #1e293b; color: #ffffff;">
                ATTENDANCE REGISTER REPORT
            </th>
        </tr>
        <tr>
            <th colspan="{{ count($dates) + 6 }}" style="font-size: 11px; text-align: center; color: #475569; height: 25px; vertical-align: middle; background-color: #f1f5f9;">
                Period: {{ $dateRangeText }} &nbsp;|&nbsp; Agency: {{ $filterAgencyName }} &nbsp;|&nbsp; Employee: {{ $filterEmployeeName }} &nbsp;|&nbsp; Generated On: {{ now()->format('d M, Y h:i A') }}
            </th>
        </tr>
        <tr>
            <th colspan="{{ count($dates) + 6 }}" style="height: 10px;"></th>
        </tr>
        <tr>
            <th style="font-weight: bold; background-color: #e2e8f0; border: 1px solid #94a3b8; text-align: center; vertical-align: middle;" rowspan="2">#</th>
            <th style="font-weight: bold; background-color: #e2e8f0; border: 1px solid #94a3b8; text-align: left; vertical-align: middle; min-width: 160px;" rowspan="2">Employee Name</th>
            <th style="font-weight: bold; background-color: #e2e8f0; border: 1px solid #94a3b8; text-align: left; vertical-align: middle; min-width: 130px;" rowspan="2">Agency</th>
            <th style="font-weight: bold; background-color: #e2e8f0; border: 1px solid #94a3b8; text-align: left; vertical-align: middle; min-width: 120px;" rowspan="2">Designation</th>
            
            {{-- Date Header row 1 (Day number) --}}
            @foreach ($dates as $d)
                <th style="font-weight: bold; background-color: {{ $d['is_weekend'] ? '#fef3c7' : '#e2e8f0' }}; border: 1px solid #94a3b8; text-align: center; width: 35px;">
                    {{ $d['day'] }}
                </th>
            @endforeach

            {{-- Summary Columns --}}
            <th style="font-weight: bold; background-color: #dcfce7; color: #166534; border: 1px solid #94a3b8; text-align: center; vertical-align: middle; width: 45px;" rowspan="2">P</th>
            <th style="font-weight: bold; background-color: #fee2e2; color: #991b1b; border: 1px solid #94a3b8; text-align: center; vertical-align: middle; width: 45px;" rowspan="2">A</th>
        </tr>
        <tr>
            {{-- Date Header row 2 (Day of week name) --}}
            @foreach ($dates as $d)
                <th style="font-size: 9px; font-weight: normal; background-color: {{ $d['is_weekend'] ? '#fef3c7' : '#f8fafc' }}; border: 1px solid #94a3b8; text-align: center;">
                    {{ substr($d['day_name'], 0, 2) }}
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @forelse ($rows as $index => $row)
            <tr>
                <td style="border: 1px solid #cbd5e1; text-align: center; vertical-align: middle;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #cbd5e1; font-weight: bold; vertical-align: middle;">{{ $row['employee_name'] }}</td>
                <td style="border: 1px solid #cbd5e1; vertical-align: middle;">{{ $row['agency_name'] }}</td>
                <td style="border: 1px solid #cbd5e1; vertical-align: middle;">{{ $row['designation_name'] }}</td>

                @foreach ($dates as $d)
                    @php
                        $status = $row['attendance'][$d['date']] ?? 'A';
                        $cellStyle = match($status) {
                            'P' => 'background-color: #dcfce7; color: #15803d;',
                            'W' => 'background-color: #fef3c7; color: #92400e;',
                            'H' => 'background-color: #e0f2fe; color: #0369a1;',
                            'L' => 'background-color: #f3e8ff; color: #7e22ce;',
                            '-' => 'background-color: #f8fafc; color: #94a3b8;',
                            default => 'background-color: #fee2e2; color: #b91c1c;',
                        };
                    @endphp
                    <td style="border: 1px solid #cbd5e1; text-align: center; vertical-align: middle; font-weight: bold; {{ $cellStyle }}">
                        {{ $status }}
                    </td>
                @endforeach

                <td style="border: 1px solid #cbd5e1; text-align: center; vertical-align: middle; font-weight: bold; background-color: #f0fdf4; color: #166534;">
                    {{ $row['total_present'] }}
                </td>
                <td style="border: 1px solid #cbd5e1; text-align: center; vertical-align: middle; font-weight: bold; background-color: #fef2f2; color: #991b1b;">
                    {{ $row['total_absent'] }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="{{ count($dates) + 6 }}" style="text-align: center; padding: 20px; font-style: italic; border: 1px solid #cbd5e1;">
                    No employee records found matching the specified filters.
                </td>
            </tr>
        @endforelse

        {{-- Daily Totals Row: Present --}}
        @if (count($rows) > 0)
            <tr>
                <th colspan="4" style="border: 1px solid #94a3b8; font-weight: bold; text-align: right; background-color: #f0fdf4; color: #166534;">
                    Total Present (P):
                </th>
                @foreach ($dates as $d)
                    <th style="border: 1px solid #94a3b8; font-weight: bold; text-align: center; background-color: #f0fdf4; color: #166534;">
                        {{ $dailyTotals[$d['date']]['P'] ?? 0 }}
                    </th>
                @endforeach
                <th style="border: 1px solid #94a3b8; font-weight: bold; text-align: center; background-color: #dcfce7; color: #166534;">
                    {{ $grandTotalPresent }}
                </th>
                <th style="border: 1px solid #94a3b8; font-weight: bold; text-align: center; background-color: #fee2e2; color: #991b1b;">
                    -
                </th>
            </tr>
            <tr>
                <th colspan="4" style="border: 1px solid #94a3b8; font-weight: bold; text-align: right; background-color: #fef2f2; color: #991b1b;">
                    Total Absent (A):
                </th>
                @foreach ($dates as $d)
                    <th style="border: 1px solid #94a3b8; font-weight: bold; text-align: center; background-color: #fef2f2; color: #991b1b;">
                        {{ $dailyTotals[$d['date']]['A'] ?? 0 }}
                    </th>
                @endforeach
                <th style="border: 1px solid #94a3b8; font-weight: bold; text-align: center; background-color: #dcfce7; color: #166534;">
                    -
                </th>
                <th style="border: 1px solid #94a3b8; font-weight: bold; text-align: center; background-color: #fee2e2; color: #991b1b;">
                    {{ $grandTotalAbsent }}
                </th>
            </tr>
        @endif
    </tbody>
</table>
