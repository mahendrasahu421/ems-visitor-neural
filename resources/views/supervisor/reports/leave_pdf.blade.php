<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supervisor Team Leave Report</title>
    <style>
        @page {
            margin: 12mm 10mm 15mm 10mm;
            size: a4 landscape;
        }
        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header-title {
            font-size: 16px;
            font-weight: bold;
            color: #1e3a8a;
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
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 10px 0 5px 0;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 3px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        table.data-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-size: 9px;
            font-weight: bold;
            padding: 5px 6px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }
        table.data-table td {
            padding: 5px 6px;
            border: 1px solid #e2e8f0;
            font-size: 8.5px;
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
        .badge-approved { background-color: #dcfce7; color: #166534; }
        .badge-rejected { background-color: #fee2e2; color: #991b1b; }
        .badge-pending  { background-color: #fef3c7; color: #92400e; }
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
                <div class="header-title">Team Leave Utilization &amp; Applications Report</div>
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
            <td width="20%">
                <div class="summary-card">
                    <div class="summary-card-title">Total Applications</div>
                    <div class="summary-card-val">{{ count($leaves) }}</div>
                </div>
            </td>
            <td width="20%">
                <div class="summary-card">
                    <div class="summary-card-title" style="color: #166534;">Approved Count</div>
                    <div class="summary-card-val" style="color: #166534;">{{ $summary['approved_count'] }}</div>
                </div>
            </td>
            <td width="20%">
                <div class="summary-card">
                    <div class="summary-card-title" style="color: #166534;">Approved Mandays</div>
                    <div class="summary-card-val" style="color: #166534;">{{ $summary['approved_total_days'] }} Days</div>
                </div>
            </td>
            <td width="20%">
                <div class="summary-card">
                    <div class="summary-card-title" style="color: #92400e;">Pending Approvals</div>
                    <div class="summary-card-val" style="color: #d97706;">{{ $summary['pending_count'] }}</div>
                </div>
            </td>
            <td width="20%">
                <div class="summary-card">
                    <div class="summary-card-title" style="color: #991b1b;">Rejected Requests</div>
                    <div class="summary-card-val" style="color: #dc2626;">{{ $summary['rejected_count'] }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- 1. Leave Applications Table -->
    <div class="section-title">Leave Applications History</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="4%">#</th>
                <th width="15%">Employee Name</th>
                <th width="12%">Designation</th>
                <th width="11%">Agency</th>
                <th width="12%">Leave Type</th>
                <th width="9%">From Date</th>
                <th width="9%">To Date</th>
                <th width="6%" style="text-align: center;">Days</th>
                <th width="9%" style="text-align: center;">Status</th>
                <th width="13%">Reason</th>
            </tr>
        </thead>
        <tbody>
            @forelse($leaves as $index => $item)
                <tr>
                    <td align="center">{{ $index + 1 }}</td>
                    <td><strong>{{ $item['employee_name'] }}</strong></td>
                    <td>{{ $item['designation'] }}</td>
                    <td>{{ $item['agency'] }}</td>
                    <td>{{ $item['leave_type'] }}</td>
                    <td>{{ $item['from_date'] }}</td>
                    <td>{{ $item['to_date'] }}</td>
                    <td align="center"><strong>{{ $item['duration'] }}</strong></td>
                    <td align="center">
                        @if($item['status'] === 'APPROVED')
                            <span class="badge badge-approved">Approved</span>
                        @elseif($item['status'] === 'REJECTED')
                            <span class="badge badge-rejected">Rejected</span>
                        @else
                            <span class="badge badge-pending">Pending</span>
                        @endif
                    </td>
                    <td>{{ $item['reason'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" align="center" style="padding: 15px; color: #94a3b8;">
                        No leave records found for the selected filter criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- 2. Team Leave Balances Grid -->
    <div class="section-title">Subordinates Leave Balances Quota (Year: {{ $year }})</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="22%">Employee Name</th>
                <th width="20%">Designation</th>
                <th width="18%">Leave Type</th>
                <th width="9%" style="text-align: center;">Allotted</th>
                <th width="8%" style="text-align: center;">Used</th>
                <th width="9%" style="text-align: center;">Pending</th>
                <th width="9%" style="text-align: center;">Remaining</th>
            </tr>
        </thead>
        <tbody>
            @php $bIdx = 1; @endphp
            @foreach($member_balances as $mb)
                @foreach($mb['balances'] as $bal)
                    <tr>
                        <td align="center">{{ $bIdx++ }}</td>
                        <td><strong>{{ $mb['employee_name'] }}</strong></td>
                        <td>{{ $mb['designation'] }}</td>
                        <td>{{ $bal['leave_name'] ?? $bal['leave_type_name'] ?? 'Leave' }}</td>
                        <td align="center">{{ $bal['allotted_days'] }}</td>
                        <td align="center" style="color: #dc2626;">{{ $bal['used_days'] }}</td>
                        <td align="center" style="color: #d97706;">{{ $bal['pending_days'] }}</td>
                        <td align="center" style="color: #16a34a; font-weight: bold;">{{ $bal['remaining_days'] }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Confidential &bull; Generated from HRMS Mobile Supervisor Portal &bull; Page 1 of 1
    </div>

</body>
</html>
