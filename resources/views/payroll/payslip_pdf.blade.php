<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payslip - {{ $payslip->payslip_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 12mm 12mm 12mm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            border: 1px solid #cbd5e1;
            padding: 16px;
            box-sizing: border-box;
        }

        /* Header */
        .company-header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 12px;
            margin-bottom: 12px;
        }

        .company-title {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .company-sub {
            font-size: 9px;
            color: #64748b;
            margin-bottom: 6px;
        }

        .payslip-badge {
            display: inline-block;
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: bold;
            padding: 4px 14px;
            border-radius: 4px;
            font-size: 11px;
            letter-spacing: 0.5px;
            border: 1px solid #cbd5e1;
        }

        /* Section Headings */
        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #1e40af;
            background-color: #eff6ff;
            padding: 4px 8px;
            margin-top: 10px;
            margin-bottom: 6px;
            border-left: 3px solid #2563eb;
            text-transform: uppercase;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 4px 6px;
            font-size: 9.5px;
            vertical-align: top;
        }

        .info-table td.label {
            color: #64748b;
            width: 18%;
            font-weight: 500;
        }

        .info-table td.val {
            color: #0f172a;
            width: 32%;
            font-weight: 600;
        }

        /* Attendance Table */
        .metric-table {
            width: 100%;
            margin-bottom: 8px;
            border: 1px solid #e2e8f0;
        }

        .metric-table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            padding: 5px;
            font-size: 8.5px;
            border: 1px solid #e2e8f0;
            text-align: center;
        }

        .metric-table td {
            padding: 6px 4px;
            font-size: 9.5px;
            font-weight: bold;
            color: #0f172a;
            border: 1px solid #e2e8f0;
            text-align: center;
        }

        .metric-table td.highlight {
            background-color: #ecfdf5;
            color: #047857;
        }

        .metric-table td.warning {
            background-color: #fef2f2;
            color: #b91c1c;
        }

        /* Breakdown Table */
        .breakdown-table {
            width: 100%;
            border: 1px solid #cbd5e1;
            margin-top: 6px;
        }

        .breakdown-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            padding: 6px 8px;
            font-size: 9.5px;
            border-bottom: 1px solid #cbd5e1;
            text-transform: uppercase;
        }

        .breakdown-table td {
            padding: 4.5px 8px;
            font-size: 9px;
            border-bottom: 1px solid #f1f5f9;
        }

        .breakdown-table td.amount {
            text-align: right;
            font-weight: 600;
        }

        .breakdown-table tr.total-row td {
            background-color: #f8fafc;
            font-weight: bold;
            font-size: 10px;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
        }

        /* Net Pay Box */
        .net-pay-box {
            background-color: #f0fdf4;
            border: 1.5px solid #86efac;
            border-radius: 4px;
            padding: 10px 14px;
            margin-top: 12px;
            margin-bottom: 12px;
        }

        .net-amount {
            font-size: 14px;
            font-weight: bold;
            color: #15803d;
        }

        .words {
            font-size: 9.5px;
            color: #374151;
            margin-top: 3px;
            font-style: italic;
        }

        /* Signatures & Footer */
        .signatures {
            margin-top: 35px;
            width: 100%;
        }

        .sign-col {
            width: 50%;
            text-align: center;
        }

        .sign-line {
            width: 180px;
            margin: 0 auto 5px auto;
            border-top: 1px solid #475569;
        }

        .footer-note {
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            margin-top: 25px;
            border-top: 1px dashed #cbd5e1;
            padding-top: 8px;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Header -->
    <div class="company-header">
        <div class="company-title">{{ $company_name }}</div>
        <div class="company-sub">{{ $company_address }}</div>
        <div class="payslip-badge">PAYSLIP FOR {{ strtoupper($period) }}</div>
    </div>

    <!-- Employee & Bank Info -->
    <div class="section-title">Employee Details</div>
    <table class="info-table">
        <tr>
            <td class="label">Employee ID:</td>
            <td class="val">{{ $employee->employee_id }}</td>
            <td class="label">Payslip No:</td>
            <td class="val">{{ $payslip->payslip_number }}</td>
        </tr>
        <tr>
            <td class="label">Employee Name:</td>
            <td class="val">{{ $employee->employee_name }}</td>
            <td class="label">Joining Date:</td>
            <td class="val">{{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('d M, Y') : 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Designation:</td>
            <td class="val">{{ $payslip->designation_name ?? 'Staff' }}</td>
            <td class="label">Department:</td>
            <td class="val">{{ $payslip->department_name ?? 'General' }}</td>
        </tr>
        <tr>
            <td class="label">Bank Name:</td>
            <td class="val">{{ $payslip->bank_name ?? 'N/A' }}</td>
            <td class="label">Bank A/C No:</td>
            <td class="val">{{ $payslip->bank_account_no ? 'XXXX-'.substr($payslip->bank_account_no, -4) : 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">IFSC Code:</td>
            <td class="val">{{ $payslip->ifsc_code ?? 'N/A' }}</td>
            <td class="label">PAN Number:</td>
            <td class="val">{{ $payslip->pan_no ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">UAN Number:</td>
            <td class="val">{{ $payslip->uan_no ?? 'N/A' }}</td>
            <td class="label">Payment Mode:</td>
            <td class="val">{{ $payslip->payment_mode ?? 'Bank Transfer' }}</td>
        </tr>
    </table>

    <!-- Attendance Metrics -->
    <div class="section-title" style="margin-top: 8px;">Attendance & Days Summary</div>
    <table class="metric-table">
        <thead>
            <tr>
                <th>Total Days</th>
                <th>Working Days</th>
                <th>Present Days</th>
                <th>Paid Leaves</th>
                <th>Holidays</th>
                <th>Weekly Offs</th>
                <th>LWP (Loss of Pay)</th>
                <th>Payable Days</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $payslip->total_days }}</td>
                <td>{{ max(0, $payslip->total_days - $payslip->weekly_off_days - $payslip->holiday_days) }}</td>
                <td>{{ number_format($payslip->present_days, 1) }}</td>
                <td>{{ number_format($payslip->paid_leave_days, 1) }}</td>
                <td>{{ number_format($payslip->holiday_days, 1) }}</td>
                <td>{{ number_format($payslip->weekly_off_days, 1) }}</td>
                <td class="{{ $payslip->lwp_days > 0 ? 'warning' : '' }}">{{ number_format($payslip->lwp_days, 1) }}</td>
                <td class="highlight">{{ number_format($payslip->payable_days, 1) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Salary Breakdown -->
    <div class="section-title">Salary Breakdown</div>
    <table class="breakdown-table">
        <thead>
            <tr>
                <th style="width: 32%;">Earnings</th>
                <th style="width: 18%; text-align: right;">Amount (INR)</th>
                <th style="width: 32%;">Deductions</th>
                <th style="width: 18%; text-align: right;">Amount (INR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Basic Salary</td>
                <td class="amount">&#8377; {{ number_format($payslip->earned_basic, 2) }}</td>
                <td>Provident Fund (PF)</td>
                <td class="amount">&#8377; {{ number_format($payslip->pf_deduction, 2) }}</td>
            </tr>
            <tr>
                <td>House Rent Allowance (HRA)</td>
                <td class="amount">&#8377; {{ number_format($payslip->earned_hra, 2) }}</td>
                <td>Employee State Insurance (ESI)</td>
                <td class="amount">&#8377; {{ number_format($payslip->esi_deduction, 2) }}</td>
            </tr>
            <tr>
                <td>Conveyance Allowance</td>
                <td class="amount">&#8377; {{ number_format($payslip->earned_conveyance, 2) }}</td>
                <td>Professional Tax (PT)</td>
                <td class="amount">&#8377; {{ number_format($payslip->pt_deduction, 2) }}</td>
            </tr>
            <tr>
                <td>Medical Allowance</td>
                <td class="amount">&#8377; {{ number_format($payslip->earned_medical, 2) }}</td>
                <td>TDS / Income Tax</td>
                <td class="amount">&#8377; {{ number_format($payslip->tds_deduction, 2) }}</td>
            </tr>
            <tr>
                <td>Bike Maintenance Allowance</td>
                <td class="amount">&#8377; {{ number_format($payslip->earned_bike_allowance ?? 1500, 2) }}</td>
                <td>Other Deductions</td>
                <td class="amount">&#8377; {{ number_format($payslip->other_deductions, 2) }}</td>
            </tr>
            <tr>
                <td>Special Allowance</td>
                <td class="amount">&#8377; {{ number_format($payslip->earned_special_allowance, 2) }}</td>
                @if($payslip->lwp_deduction > 0)
                <td>Loss of Pay (LWP)</td>
                <td class="amount">&#8377; {{ number_format($payslip->lwp_deduction, 2) }}</td>
                @else
                <td></td>
                <td class="amount"></td>
                @endif
            </tr>
            @if($payslip->bonus > 0 || $payslip->incentive > 0 || $payslip->overtime_pay > 0)
            <tr>
                <td>Bonus / Incentives</td>
                <td class="amount">&#8377; {{ number_format($payslip->bonus + $payslip->incentive + $payslip->overtime_pay, 2) }}</td>
                <td></td>
                <td class="amount"></td>
            </tr>
            @endif
            <tr class="total-row">
                <td><strong>Total Gross Earnings</strong></td>
                <td class="amount"><strong>&#8377; {{ number_format($payslip->total_earnings, 2) }}</strong></td>
                <td><strong>Total Deductions</strong></td>
                <td class="amount"><strong>&#8377; {{ number_format($payslip->total_deductions, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    @if(($payslip->gratuity_accrual ?? 0) > 0)
    <div style="font-size: 8px; color: #475569; margin-top: 4px; padding: 2px 4px; font-style: italic;">
        * Note: Monthly Employer Gratuity Provision: &#8377; {{ number_format($payslip->gratuity_accrual, 2) }} (Accrued retiral benefit as part of Annual CTC; not deducted from in-hand net salary).
    </div>
    @endif

    <!-- Net Pay Summary -->
    <div class="net-pay-box">
        <table style="width: 100%;">
            <tr>
                <td style="width: 60%;">
                    <div style="font-size: 10px; color: #166534; font-weight: bold; text-transform: uppercase;">Net Payable Salary:</div>
                    <div class="words">In Words: {{ $payslip->net_salary_in_words }}</div>
                </td>
                <td style="width: 40%; text-align: right;">
                    <div class="net-amount">&#8377; {{ number_format($payslip->net_salary, 2) }}</div>
                    <div style="font-size: 8.5px; color: #4b5563; margin-top: 2px;">Status: <strong>{{ strtoupper($payslip->payment_status) }}</strong></div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Signatures -->
    <table class="signatures">
        <tr>
            <td class="sign-col">
                <div class="sign-line"></div>
                <div style="font-size: 9px; font-weight: bold; color: #334155;">Employee Signature</div>
            </td>
            <td class="sign-col">
                <div class="sign-line"></div>
                <div style="font-size: 9px; font-weight: bold; color: #334155;">Authorized Signatory / HR</div>
            </td>
        </tr>
    </table>

    <!-- Disclaimer -->
    <div class="footer-note">
        This is a computer-generated payslip and does not require a physical signature if validated electronically.<br>
        Confidential &bull; Generated on {{ now()->format('d M Y, h:i A') }} &bull; For queries contact HR or Finance.
    </div>
</div>

</body>
</html>
