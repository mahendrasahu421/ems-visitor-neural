<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Leave Application Submitted - Under Review' }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; -webkit-font-smoothing: antialiased;">

    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; padding: 30px 15px;">
        <tr>
            <td align="center">
                
                <!-- Main Container -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); border: 1px solid #e2e8f0;">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); padding: 25px 30px; text-align: left;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="vertical-align: middle;">
                                        <div style="color: #bae6fd; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">
                                            VProtect EMS &bull; Leave Notification
                                        </div>
                                        <h1 style="color: #ffffff; font-size: 20px; font-weight: 700; margin: 0; line-height: 1.3;">
                                            Leave Application Submitted
                                        </h1>
                                    </td>
                                    @if(!empty($companyDetails) && !empty($companyDetails->company_logo))
                                    <td align="right" style="vertical-align: middle;">
                                        <img src="{{ asset('uploads/company/' . $companyDetails->company_logo) }}" alt="Logo" style="max-height: 45px; max-width: 120px; object-fit: contain; background: #ffffff; padding: 4px 8px; border-radius: 6px;">
                                    </td>
                                    @endif
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Status Banner -->
                    <tr>
                        <td style="background-color: #fefce8; border-bottom: 1px solid #fef08a; padding: 14px 30px;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="color: #854d0e; font-size: 13px; font-weight: 600;">
                                        &#9203; Your leave request has been submitted and is currently <strong>Under Review</strong>.
                                    </td>
                                    <td align="right">
                                        <span style="background-color: #fde047; color: #713f12; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.5px;">
                                            UNDER REVIEW
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 25px 30px;">
                            
                            <p style="font-size: 15px; line-height: 1.6; color: #334155; margin-top: 0; margin-bottom: 16px;">
                                Dear <strong>{{ $employee->employee_name ?? 'Colleague' }}</strong>,
                            </p>
                            
                            <p style="font-size: 14px; line-height: 1.6; color: #334155; margin-bottom: 20px;">
                                Your leave application has been recorded in the system. It has been routed to your reporting senior/supervisor <strong>{{ $supervisorName }}</strong> for review and approval.
                            </p>

                            @if(!empty($template_body))
                            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 20px; font-size: 14px; line-height: 1.6; color: #334155;">
                                {!! $template_body !!}
                            </div>
                            @endif

                            <!-- Leave Details Card -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 24px; font-size: 13px;">
                                <tr>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 600; width: 38%;">Leave Type:</td>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #0284c7; font-weight: 700;">
                                        {{ $leaveType->leave_name ?? 'Leave' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 600;">Leave Period:</td>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #0f172a; font-weight: 600;">
                                        {{ \Carbon\Carbon::parse($leave->from_date)->format('d M, Y') }} &rarr; {{ \Carbon\Carbon::parse($leave->to_date)->format('d M, Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 600;">Total Duration:</td>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #0f172a; font-weight: 600;">
                                        {{ $leave->duration ?: (\Carbon\Carbon::parse($leave->from_date)->diffInDays(\Carbon\Carbon::parse($leave->to_date)) + 1) }} Day(s)
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 600;">Reporting Senior / Reviewer:</td>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #334155; font-weight: 600;">
                                        {{ $supervisorName }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 600;">Reason:</td>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #334155;">
                                        {{ $leave->reason ?: 'No reason provided' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 600;">Submitted On:</td>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #334155;">
                                        {{ $leave->created_at ? $leave->created_at->format('d M, Y h:i A') : now()->format('d M, Y h:i A') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 16px; color: #64748b; font-weight: 600;">Status:</td>
                                    <td style="padding: 12px 16px;">
                                        <span style="display: inline-block; background-color: #fef3c7; color: #92400e; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px;">
                                            PENDING REVIEW
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 14px 18px; margin-bottom: 20px;">
                                <p style="font-size: 13px; line-height: 1.5; color: #166534; margin: 0;">
                                    &#10003; <strong>What's Next?</strong> You will be notified via email and push notification once your reporting supervisor has reviewed your application.
                                </p>
                            </div>

                            <p style="font-size: 13px; line-height: 1.6; color: #64748b; margin-bottom: 0;">
                                Best regards,<br>
                                <strong>HR &amp; Operations Team</strong><br>
                                {{ $companyDetails->company_name ?? 'VProtect India' }}
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 18px 30px; text-align: center;">
                            <p style="font-size: 11px; line-height: 1.5; color: #94a3b8; margin: 0;">
                                This is an automated notification from {{ $companyDetails->company_name ?? 'VProtect India' }} HRMS Portal.<br>
                                Please do not reply directly to this email.
                            </p>
                        </td>
                    </tr>

                </table>
                <!-- /Main Container -->

            </td>
        </tr>
    </table>

</body>
</html>
