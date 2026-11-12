<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Increment Letter - {{ $increment->employee->employee_name }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
            font-size: 13px;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
        }
        .company-sub {
            font-size: 11px;
            color: #666666;
        }
        .ref-box {
            text-align: right;
            font-size: 11px;
            color: #555555;
        }
        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 20px 0;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        .recipient {
            margin-bottom: 20px;
            font-size: 12px;
        }
        .recipient strong {
            font-size: 14px;
            color: #111827;
        }
        .content {
            margin-bottom: 20px;
            text-align: justify;
        }
        .table-box {
            width: 100%;
            border-collapse: collapse;
            margin: 18px 0;
        }
        .table-box th {
            background-color: #f3f4f6;
            color: #374151;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: bold;
            padding: 8px 10px;
            border: 1px solid #d1d5db;
        }
        .table-box td {
            font-size: 12px;
            padding: 8px 10px;
            border: 1px solid #d1d5db;
        }
        .text-right {
            text-align: right;
        }
        .promo-box {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            padding: 10px 14px;
            margin: 15px 0;
            font-size: 12px;
            color: #166534;
        }
        .note-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 10px 14px;
            margin: 15px 0;
            font-style: italic;
            font-size: 12px;
            color: #334155;
        }
        .signatures {
            margin-top: 40px;
            width: 100%;
        }
        .sig-block {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }
    </style>
</head>
<body>

    <table width="100%" class="header">
        <tr>
            <td valign="top">
                <div class="company-name">{{ $company_name }}</div>
                <div class="company-sub">People Operations &amp; Human Resources Division</div>
            </td>
            <td valign="top" class="ref-box">
                <strong>Ref No:</strong> {{ $increment->increment_number }}<br>
                <strong>Date:</strong> {{ now()->format('F d, Y') }}
            </td>
        </tr>
    </table>

    <div class="recipient">
        <strong>To,</strong><br>
        <strong>{{ $increment->employee->employee_name }}</strong><br>
        Employee ID: {{ $increment->employee->employee_id }}<br>
        Designation: {{ $increment->previousDesignation->name ?? 'Staff' }}<br>
        Department / Agency: {{ $increment->employee->agency->agency_name ?? 'In-House' }}
    </div>

    <div class="title">
        Annual Appraisal &amp; Compensation Revision Letter
    </div>

    <div class="content">
        <p>Dear <strong>{{ $increment->employee->employee_name }}</strong>,</p>

        <p>
            In recognition of your sincere dedication, valued contribution, and performance during the
            <strong>{{ $increment->review_cycle }}</strong>, the Management is pleased to revise your annual compensation structure.
        </p>

        @if($increment->isPromotion())
            <div class="promo-box">
                <strong>Congratulations on your Promotion!</strong><br>
                Along with your salary revision, you are being promoted to the position of
                <strong>{{ $increment->newDesignation->name }}</strong> effective
                <strong>{{ $increment->effective_date->format('F d, Y') }}</strong>.
            </div>
        @endif

        <p>
            Your revised compensation will be effective from
            <strong>{{ $increment->effective_date->format('F d, Y') }}</strong>.
            The revised compensation breakdown is outlined below:
        </p>

        <table class="table-box">
            <thead>
                <tr>
                    <th>Component</th>
                    <th class="text-right">Pre-Revision (INR)</th>
                    <th class="text-right">Revised (INR)</th>
                    <th class="text-right">Hike Difference</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Monthly Gross Salary</strong></td>
                    <td class="text-right">&#8377; {{ number_format($increment->current_gross, 2) }}</td>
                    <td class="text-right"><strong>&#8377; {{ number_format($increment->new_gross, 2) }}</strong></td>
                    <td class="text-right">+&#8377; {{ number_format(max(0, $increment->new_gross - $increment->current_gross), 2) }} / mo</td>
                </tr>
                <tr>
                    <td><strong>Annual Cost to Company (CTC)</strong></td>
                    <td class="text-right">&#8377; {{ number_format($increment->current_ctc, 2) }}</td>
                    <td class="text-right"><strong>&#8377; {{ number_format($increment->new_ctc, 2) }}</strong></td>
                    <td class="text-right">+&#8377; {{ number_format($increment->hike_amount, 2) }} / yr</td>
                </tr>
                <tr>
                    <td><strong>Percentage Hike</strong></td>
                    <td class="text-right">-</td>
                    <td class="text-right"><strong>{{ number_format($increment->hike_percentage, 1) }}%</strong></td>
                    <td class="text-right">{{ number_format($increment->hike_percentage, 1) }}% Hike</td>
                </tr>
                @if($increment->isPromotion())
                    <tr>
                        <td><strong>Designation</strong></td>
                        <td class="text-right">{{ $increment->previousDesignation->name ?? 'Staff' }}</td>
                        <td class="text-right"><strong>{{ $increment->newDesignation->name }}</strong></td>
                        <td class="text-right">Promoted</td>
                    </tr>
                @endif
            </tbody>
        </table>

        @if($increment->letter_notes)
            <div class="note-box">
                <strong>Management Note:</strong><br>
                "{{ $increment->letter_notes }}"
            </div>
        @endif

        <p>
            All other terms and conditions governing your employment contract remain unchanged.
            Kindly note that your compensation package is strictly confidential between you and the organization.
        </p>

        <p>
            We thank you for your committed service and look forward to your continuing growth and success with our organization.
        </p>

        <p>
            Sincerely,
        </p>
    </div>

    <table width="100%" class="signatures">
        <tr>
            <td width="50%" valign="top">
                <br><br><br>
                <strong>Authorized Signatory</strong><br>
                Human Resources Management<br>
                {{ $company_name }}
            </td>
            <td width="50%" valign="top" style="text-align: right;">
                <br><br><br>
                <strong>{{ $increment->employee->employee_name }}</strong><br>
                Employee Signature &amp; Date<br>
                (Acknowledged &amp; Accepted)
            </td>
        </tr>
    </table>

</body>
</html>
