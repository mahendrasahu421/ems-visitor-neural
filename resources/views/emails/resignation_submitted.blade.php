<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resignation Notification</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; color: #1e293b; }
        .container { max-width: 640px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: #ffffff; padding: 24px 30px; }
        .header h2 { margin: 0 0 6px; font-size: 20px; font-weight: 700; }
        .header p { margin: 0; opacity: 0.9; font-size: 13px; }
        .content { padding: 30px; }
        .alert-pill { display: inline-block; padding: 6px 14px; background: #fef3c7; color: #92400e; border-radius: 20px; font-size: 12px; font-weight: 700; margin-bottom: 20px; border: 1px solid #fde68a; }
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .meta-table td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 13px; vertical-align: top; }
        .meta-table td.label { width: 38%; color: #64748b; font-weight: 600; }
        .meta-table td.val { color: #0f172a; font-weight: 600; }
        .reason-box { background: #f8fafc; border-left: 4px solid #3b82f6; padding: 16px; border-radius: 6px; margin-bottom: 24px; }
        .reason-box h4 { margin: 0 0 8px; font-size: 13px; color: #334155; text-transform: uppercase; letter-spacing: 0.5px; }
        .reason-box p { margin: 0; font-size: 13px; line-height: 1.6; color: #475569; white-space: pre-line; }
        .btn-group { margin: 28px 0 10px; text-align: center; }
        .btn { display: inline-block; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 13px; margin: 0 6px 8px; }
        .btn-accept { background: #10b981; color: #ffffff !important; }
        .btn-reject { background: #ef4444; color: #ffffff !important; }
        .btn-view { background: #0f172a; color: #ffffff !important; }
        .footer { background: #f8fafc; padding: 20px 30px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
        .cc-list { background: #f1f5f9; padding: 8px 12px; border-radius: 6px; font-size: 12px; color: #475569; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{ $companyDetails->company_name ?? 'Neural Info Solutions' }}</h2>
            <p>Employee Resignation & Exit Notification</p>
        </div>
        <div class="content">
            <div class="alert-pill">
                @if ($resignation->initiated_by === 'employee')
                    🔔 Resignation Submitted by Employee
                @else
                    ⚠️ Exit / Separation Initiated by Supervisor / Admin
                @endif
            </div>

            <p style="font-size: 14px; line-height: 1.6; margin-top: 0;">
                Hello,
            </p>
            <p style="font-size: 14px; line-height: 1.6; color: #334155;">
                This is a formal notification that an employee resignation/separation request has been submitted in the HR system with the following particulars:
            </p>

            <table class="meta-table">
                <tr>
                    <td class="label">Reference ID</td>
                    <td class="val"><strong style="color: #2563eb;">{{ $resignation->resignation_code }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Employee Name</td>
                    <td class="val">{{ $employee->employee_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Employee Email</td>
                    <td class="val">{{ $employee->employee_email ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Designation</td>
                    <td class="val">{{ $employee->designation->name ?? ($employee->designation->designation_name ?? 'Staff') }}</td>
                </tr>
                <tr>
                    <td class="label">Reporting Supervisor</td>
                    <td class="val">{{ $supervisor->employee_name ?? 'Unassigned' }}</td>
                </tr>
                <tr>
                    <td class="label">Submission Date</td>
                    <td class="val">{{ \Carbon\Carbon::parse($resignation->resignation_date)->format('d M, Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Notice Period</td>
                    <td class="val">{{ $resignation->notice_period_days }} Days</td>
                </tr>
                <tr>
                    <td class="label">Requested Last Working Day</td>
                    <td class="val" style="color: #b91c1c;">{{ \Carbon\Carbon::parse($resignation->requested_last_working_day)->format('d M, Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Resignation Reason</td>
                    <td class="val">{{ $resignation->reason }}</td>
                </tr>
            </table>

            @if (!empty($resignation->reason_details))
                <div class="reason-box">
                    <h4>Resignation Letter / Statement:</h4>
                    <p>{{ $resignation->reason_details }}</p>
                </div>
            @endif

            @if (!empty($resignation->cc_emails) && count($resignation->clean_cc_emails) > 0)
                <div class="cc-list">
                    <strong>CC Recipients:</strong> {{ implode(', ', $resignation->clean_cc_emails) }}
                </div>
            @endif

            <p style="font-size: 13px; color: #64748b; margin-top: 24px; text-align: center;">
                You can directly respond to this resignation below, or login to the HR portal:
            </p>

            <!-- Direct Response Buttons -->
            <div class="btn-group">
                <a href="{{ $acceptUrl }}" class="btn btn-accept" target="_blank">
                    ✓ Accept Resignation
                </a>
                <a href="{{ $rejectUrl }}" class="btn btn-reject" target="_blank">
                    ✕ Reject Resignation
                </a>
                <a href="{{ $viewUrl }}" class="btn btn-view" target="_blank">
                    View in Portal
                </a>
            </div>

            <p style="font-size: 11px; color: #94a3b8; text-align: center; margin-top: 15px;">
                Note: Clicking Accept or Reject above opens a secure confirmation window to finalize relieving dates and remarks.
            </p>
        </div>
        <div class="footer">
            <p style="margin: 0 0 4px;">{{ $companyDetails->company_name ?? 'VProtect EMS' }} &bull; Automated HR Notification</p>
            <p style="margin: 0;">Please do not reply directly to this automated email.</p>
        </div>
    </div>
</body>
</html>
