<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Register Report</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm 8mm 10mm 8mm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8px;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }

        .report-header {
            margin-bottom: 8px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 6px;
        }

        .report-title {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 4px 0;
        }

        .report-meta {
            font-size: 8px;
            color: #475569;
        }

        .report-meta span {
            margin-right: 12px;
        }

        .report-meta strong {
            color: #0f172a;
        }

        table.register-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.register-table th,
        table.register-table td {
            border: 0.5px solid #cbd5e1;
            padding: 2.5px 1px;
            text-align: center;
            vertical-align: middle;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        table.register-table thead th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: bold;
            font-size: 7.5px;
        }

        table.register-table thead th.weekend {
            background-color: #fef3c7;
            color: #92400e;
        }

        table.register-table td.emp-name {
            text-align: left;
            padding-left: 3px;
            font-weight: bold;
            font-size: 8px;
            color: #0f172a;
        }

        table.register-table td.emp-sub {
            text-align: left;
            padding-left: 3px;
            font-size: 7px;
            color: #475569;
        }

        .status-p {
            color: #15803d;
            font-weight: bold;
            background-color: #f0fdf4;
        }

        .status-a {
            color: #b91c1c;
            font-weight: bold;
            background-color: #fef2f2;
        }

        .status-w {
            color: #92400e;
            font-weight: bold;
            background-color: #fef3c7;
        }

        .status-h {
            color: #0369a1;
            font-weight: bold;
            background-color: #e0f2fe;
        }

        .status-l {
            color: #7e22ce;
            font-weight: bold;
            background-color: #f3e8ff;
        }

        .status-dash {
            color: #94a3b8;
            background-color: #f8fafc;
        }

        .col-total-p {
            background-color: #dcfce7 !important;
            color: #166534 !important;
            font-weight: bold;
        }

        .col-total-a {
            background-color: #fee2e2 !important;
            color: #991b1b !important;
            font-weight: bold;
        }

        .totals-row th {
            font-size: 7.5px;
            padding: 3px 1px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 12px;
            font-size: 7px;
            color: #94a3b8;
            text-align: right;
            border-top: 0.5px solid #e2e8f0;
            padding-top: 3px;
        }

        .legend {
            margin-top: 6px;
            font-size: 7.5px;
            color: #475569;
        }

        .legend-badge {
            display: inline-block;
            padding: 1px 4px;
            font-weight: bold;
            border-radius: 2px;
            margin-right: 4px;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <div class="report-header">
        <div style="float: right; text-align: right; font-size: 7.5px; color: #64748b;">
            Generated: {{ now()->format('d-m-Y H:i') }}
        </div>
        <div class="report-title">Monthly Attendance Register</div>
        <div class="report-meta">
            <span><strong>Period:</strong> {{ $dateRangeText }}</span>
            <span><strong>Agency:</strong> {{ $filterAgencyName }}</span>
            <span><strong>Employee:</strong> {{ $filterEmployeeName }}</span>
            <span><strong>Total Staff:</strong> {{ count($rows) }}</span>
            <span><strong>Grand Present:</strong> {{ $grandTotalPresent }}</span>
            <span><strong>Grand Absent:</strong> {{ $grandTotalAbsent }}</span>
        </div>
    </div>

    <!-- Attendance Register Matrix Table -->
    <table class="register-table">
        <thead>
            <tr>
                <th style="width: 14px;" rowspan="2">#</th>
                <th style="width: 85px; text-align: left; padding-left: 3px;" rowspan="2">Employee</th>
                <th style="width: 60px; text-align: left; padding-left: 3px;" rowspan="2">Agency</th>
                <th style="width: 55px; text-align: left; padding-left: 3px;" rowspan="2">Designation</th>

                @foreach ($dates as $d)
                    <th class="{{ $d['is_weekend'] ? 'weekend' : '' }}" style="width: {{ count($dates) > 25 ? '13px' : '18px' }};">
                        {{ $d['day'] }}
                    </th>
                @endforeach

                <th class="col-total-p" style="width: 18px;" rowspan="2">P</th>
                <th class="col-total-a" style="width: 18px;" rowspan="2">A</th>
            </tr>
            <tr>
                @foreach ($dates as $d)
                    <th class="{{ $d['is_weekend'] ? 'weekend' : '' }}" style="font-size: 6.5px; font-weight: normal;">
                        {{ substr($d['day_name'], 0, 1) }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="emp-name">{{ $row['employee_name'] }}</td>
                    <td class="emp-sub">{{ $row['agency_name'] }}</td>
                    <td class="emp-sub">{{ $row['designation_name'] }}</td>

                    @foreach ($dates as $d)
                        @php
                            $status = $row['attendance'][$d['date']] ?? '-';
                            $statusClass = match($status) {
                                'P' => 'status-p',
                                'W' => 'status-w',
                                'H' => 'status-h',
                                'L' => 'status-l',
                                '-' => 'status-dash',
                                default => 'status-a',
                            };
                        @endphp
                        <td class="{{ $statusClass }}">
                            {{ $status }}
                        </td>
                    @endforeach

                    <td class="col-total-p">{{ $row['total_present'] }}</td>
                    <td class="col-total-a">{{ $row['total_absent'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($dates) + 6 }}" style="padding: 15px; text-align: center; color: #64748b;">
                        No employee records found matching the specified filters.
                    </td>
                </tr>
            @endforelse

            {{-- Summary rows --}}
            @if (count($rows) > 0)
                <tr class="totals-row" style="background-color: #f0fdf4;">
                    <th colspan="4" style="text-align: right; padding-right: 4px; color: #166534;">Total Present (P)</th>
                    @foreach ($dates as $d)
                        <th style="color: #166534;">{{ $dailyTotals[$d['date']]['P'] ?? 0 }}</th>
                    @endforeach
                    <th class="col-total-p">{{ $grandTotalPresent }}</th>
                    <th style="background-color: #f8fafc; color: #94a3b8;">-</th>
                </tr>
                <tr class="totals-row" style="background-color: #fef2f2;">
                    <th colspan="4" style="text-align: right; padding-right: 4px; color: #991b1b;">Total Absent (A)</th>
                    @foreach ($dates as $d)
                        <th style="color: #991b1b;">{{ $dailyTotals[$d['date']]['A'] ?? 0 }}</th>
                    @endforeach
                    <th style="background-color: #f8fafc; color: #94a3b8;">-</th>
                    <th class="col-total-a">{{ $grandTotalAbsent }}</th>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Legend -->
    <div class="legend">
        <strong>Legend:</strong>
        <span class="legend-badge status-p">P</span> Present &nbsp;&bull;&nbsp;
        <span class="legend-badge status-a">A</span> Absent (Working Day) &nbsp;&bull;&nbsp;
        <span class="legend-badge status-w">W</span> Weekend &nbsp;&bull;&nbsp;
        <span class="legend-badge status-h">H</span> Holiday &nbsp;&bull;&nbsp;
        <span class="legend-badge status-l">L</span> Approved Leave &nbsp;&bull;&nbsp;
        <span class="legend-badge status-dash">-</span> Future / Pre-joining
    </div>

    <!-- Footer -->
    <div class="footer">
        Attendance Register &bull; Page 1 of 1
    </div>

</body>
</html>
