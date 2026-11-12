<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resignation Decision Notification</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; color: #1e293b; }
        .container { max-width: 640px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
        .header { background: {{ $resignation->status === 'approved' ? 'linear-gradient(135deg, #065f46 0%, #10b981 100%)' : 'linear-gradient(135deg, #991b1b 0%, #ef4444 100%)' }}; color: #ffffff; padding: 24px 30px; }
        .header h2 { margin: 0 0 6px; font-size: 20px; font-weight: 700; }
        .header p { margin: 0; opacity: 0.9; font-size: 13px; }
        .content { padding: 30px; }
        .status-badge { display: inline-block; padding: 8px 18px; border-radius: 20px; font-size: 13px; font-weight: 700; margin-bottom: 20px; {{ $resignation->status === 'approved' ? 'background: #dcfce7; color: #166534; border: 1px solid #bbf7d0;' : 'background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;' }} }
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .meta-table td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 13px; vertical-align: top; }
        .meta-table td.label { width: 38%; color: #64748b; font-weight: 600; }
        .meta-table td.val { color: #0f172a; font-weight: 600; }
        .remarks-box { background: #f8fafc; border-left: 4px solid {{ $resignation->status === 'approved' ? '#10b981' : '#ef4444' }}; padding: 16px; border-radius: 6px; margin-bottom: 24px; }
        .remarks-box h4 { margin: 0 0 8px; font-size: 13px; color: #334155; text-transform: uppercase; }
        .remarks-box p { margin: 0; font-size: 13px; line-height: 1.6; color: #475569; white-space: pre-line; }
        .footer { background: #f8fafc; padding: 20px 30px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{ $companyDetails->company_name ?? 'Neural Info Solutions' }}</h2>
            <p>Resignation Decision Notification</p>
        </div>
        <div class="content">
            <div class="status-badge">
                @if ($resignation->status === 'approved')
                    ✓ Resignation Formally Accepted & Approved
                @else
                    ✕ Resignation Request Rejected / Declined
                @endif
            </div>

            <p style="font-size: 14px; line-height: 1.6; margin-top: 0;">
                Dear <strong>{{ $employee->employee_name ?? 'Employee' }}</strong>,
            </p>
            <p style="font-size: 14px; line-height: 1.6; color: #334155;">
                @if ($resignation->status === 'approved')
                    Your resignation request (Ref: <strong>{{ $resignation->resignation_code }}</strong>) has been reviewed and formally <strong>accepted</strong> by management.
                @else
                    Your resignation request (Ref: <strong>{{ $resignation->resignation_code }}</strong>) has been reviewed by management and has been <strong>rejected / retained</strong>.
                @endif
            </p>

            <table class="meta-table">
                <tr>
                    <td class="label">Reference ID</td>
                    <td class="val">{{ $resignation->resignation_code }}</td>
                </tr>
                <tr>
                    <td class="label">Reviewed By</td>
                    <td class="val">{{ $resignation->action_by_name ?? 'Management / HR' }} ({{ ucfirst($resignation->action_by_type ?? 'HR') }})</td>
                </tr>
                <tr>
                    <td class="label">Decision Date</td>
                    <td class="val">{{ $resignation->action_at ? \Carbon\Carbon::parse($resignation->action_at)->format('d M, Y h:i A') : now()->format('d M, Y') }}</td>
                </tr>
                @if ($resignation->status === 'approved')
                    <tr>
                        <td class="label">Approved Last Working Day</td>
                        <td class="val" style="color: #059669; font-size: 15px;">
                            <strong>{{ \Carbon\Carbon::parse($resignation->effectiveLastWorkingDay())->format('d M, Y') }}</strong>
                        </td>
                    </tr>
                @endif
            </table>

            @if (!empty($resignation->action_remarks))
                <div class="remarks-box">
                    <h4>Reviewer Remarks / Exit Instructions:</h4>
                    <p>{{ $resignation->action_remarks }}</p>
                </div>
            @endif

            @if ($resignation->status === 'approved')
                <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 14px 18px; margin-top: 20px;">
                    <h5 style="margin: 0 0 6px; color: #065f46; font-size: 13px; font-weight: 700;">Handover & Relieving Guidelines:</h5>
                    <ul style="margin: 0; padding-left: 20px; font-size: 12px; color: #047857; line-height: 1.6;">
                        <li>Ensure comprehensive handover of active tasks, files, and credentials to your supervisor.</li>
                        <li>Return all company assets, ID cards, and access keys on or before your last working day.</li>
                        <li>Your final settlement (Full & Final) will be computed post completion of all clearance sign-offs.</li>
                    </ul>
                </div>
            @endif
        </div>
        <div class="footer">
            <p style="margin: 0 0 4px;">{{ $companyDetails->company_name ?? 'VProtect EMS' }} &bull; Human Resources Department</p>
            <p style="margin: 0;">Automated message generated by Chitragupta HRMS.</p>
        </div>
    </div>
</body>
</html>
