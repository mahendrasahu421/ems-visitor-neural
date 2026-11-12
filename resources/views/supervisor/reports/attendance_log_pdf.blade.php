<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supervisor Team Attendance Punch Logs Report</title>
    <style>
        @page {
            margin: 12mm 10mm 15mm 10mm;
            size: a4 landscape;
        }
        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            font-size: 9.5px;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f766e;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header-title {
            font-size: 15px;
            font-weight: bold;
            color: #0f766e;
            margin: 0;
            text-transform: uppercase;
        }
        .header-meta {
            font-size: 9px;
            color: #64748b;
            margin-top: 3px;
        }
        .summary-box {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
        }
        .summary-card {
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            padding: 6px 10px;
            text-align: center;
            border-radius: 4px;
        }
        .summary-card-title {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
        }
        .summary-card-val {
            font-size: 14px;
            font-weight: bold;
            margin-top: 2px;
            color: #0f172a;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        table.data-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-size: 8.5px;
            font-weight: bold;
            padding: 5px 6px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }
        table.data-table td {
            padding: 5px 6px;
            border: 1px solid #e2e8f0;
            font-size: 8px;
            vertical-align: middle;
        }
        table.data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-present  { background-color: #dcfce7; color: #166534; }
        .badge-checkedin{ background-color: #e0f2fe; color: #0369a1; }
        .badge-late     { background-color: #fef3c7; color: #92400e; }
        .footer {
            position: fixed;
            bottom: -5mm;
            left: 0;
            right: 0;
            font-size: 8px;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="vertical-align: middle;">
                <div class="header-title">Team Attendance Punch Logs Report</div>
                <div class="header-meta">
                    <strong>Supervisor:</strong> {{ $supervisor->employee_name }} (ID: {{ $supervisor->employee_id }}) &nbsp;|&nbsp;
                    <strong>Period:</strong> {{ $periodText }} &nbsp;|&nbsp;
                    <strong>Generated:</strong> {{ now()->format('d M, Y h:i A') }}
                </div>
            </td>
            @if(!empty($companyDetails) && !empty($companyDetails->company_logo))
            <td align="right" style="vertical-align: middle;">
                <img src="{{ public_path('uploads/company/' . $companyDetails->company_logo) }}" alt="Logo" style="max-height: 40px; max-width: 120px;">
            </td>
            @endif
        </tr>
    </table>

    <!-- Summary Metrics -->
    <table class="summary-box" cellpadding="0" cellspacing="4">
        <tr>
            <td width="25%">
                <div class="summary-card">
                    <div class="summary-card-title">Total Punch Logs</div>
                    <div class="summary-card-val">{{ $summary['total_records'] }}</div>
                </div>
            </td>
            <td width="25%">
                <div class="summary-card">
                    <div class="summary-card-title" style="color: #0f766e;">Total Hours Worked</div>
                    <div class="summary-card-val" style="color: #0f766e;">{{ $summary['total_hours_worked'] }} hrs</div>
                </div>
            </td>
            <td width="25%">
                <div class="summary-card">
                    <div class="summary-card-title" style="color: #dc2626;">Late Arrivals</div>
                    <div class="summary-card-val" style="color: #dc2626;">{{ $summary['late_arrivals'] }}</div>
                </div>
            </td>
            <td width="25%">
                <div class="summary-card">
                    <div class="summary-card-title" style="color: #16a34a;">Early Arrivals</div>
                    <div class="summary-card-val" style="color: #16a34a;">{{ $summary['early_arrivals'] }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Punch Logs Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="4%">#</th>
                <th width="16%">Employee Name</th>
                <th width="13%">Designation</th>
                <th width="12%">Agency</th>
                <th width="9%">Date</th>
                <th width="7%">Day</th>
                <th width="9%" style="text-align: center;">Check In</th>
                <th width="9%" style="text-align: center;">Check Out</th>
                <th width="8%" style="text-align: center;">Worked</th>
                <th width="7%" style="text-align: center;">Status</th>
                <th width="14%">Punctuality</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $rec)
                <tr>
                    <td align="center">{{ $index + 1 }}</td>
                    <td><strong>{{ $rec['employee']['name'] }}</strong></td>
                    <td>{{ $rec['employee']['designation'] }}</td>
                    <td>{{ $rec['employee']['agency'] }}</td>
                    <td>{{ $rec['date'] }}</td>
                    <td>{{ $rec['day'] }}</td>
                    <td align="center" style="color: #0284c7; font-weight: bold;">{{ $rec['check_in_time'] ?: '-' }}</td>
                    <td align="center">{{ $rec['check_out_time'] ?: '-' }}</td>
                    <td align="center"><strong>{{ $rec['working_formatted'] }}</strong></td>
                    <td align="center">
                        @if($rec['status'] === 'PRESENT')
                            <span class="badge badge-present">Present</span>
                        @elseif($rec['status'] === 'CHECKED_IN')
                            <span class="badge badge-checkedin">In Office</span>
                        @else
                            <span class="badge badge-late">{{ $rec['status'] }}</span>
                        @endif
                    </td>
                    <td>
                        @if($rec['punctuality']['is_late'])
                            <span style="color: #dc2626; font-weight: bold;">Late ({{ $rec['punctuality']['late_formatted'] }})</span>
                        @elseif($rec['punctuality']['is_early_in'])
                            <span style="color: #16a34a;">Early ({{ $rec['punctuality']['early_in_minutes'] }}m)</span>
                        @else
                            On Time
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" align="center" style="padding: 15px; color: #94a3b8;">
                        No attendance punch logs found for the selected period.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Confidential &bull; Generated from HRMS Mobile Supervisor Portal &bull; Page 1 of 1
    </div>

</body>
</html>
