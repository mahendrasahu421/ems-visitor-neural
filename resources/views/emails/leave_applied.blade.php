<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'New Leave Application' }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; -webkit-font-smoothing: antialiased;">

    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; padding: 30px 15px;">
        <tr>
            <td align="center">
                
                <!-- Main Container -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); border: 1px solid #e2e8f0;">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); padding: 25px 30px; text-align: left;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="vertical-align: middle;">
                                        <div style="color: #93c5fd; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">
                                            VProtect EMS &bull; Leave Notification
                                        </div>
                                        <h1 style="color: #ffffff; font-size: 20px; font-weight: 700; margin: 0; line-height: 1.3;">
                                            New Leave Request
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

                    <!-- Alert Banner -->
                    <tr>
                        <td style="background-color: #eff6ff; border-bottom: 1px solid #dbeafe; padding: 12px 30px;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="color: #1e40af; font-size: 13px; font-weight: 600;">
                                        &#9888; Action Required: A new leave request is pending review.
                                    </td>
                                    <td align="right">
                                        <span style="background-color: #fef3c7; color: #92400e; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.5px;">
                                            PENDING
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 25px 30px;">
                            
                            <p style="font-size: 14px; line-height: 1.6; color: #334155; margin-top: 0; margin-bottom: 20px;">
                                Dear <strong>{{ $supervisorName ?: ($recipientRole ?: 'Supervisor / HR') }}</strong>,
                            </p>
                            
                            <p style="font-size: 14px; line-height: 1.6; color: #334155; margin-bottom: 20px;">
                                Employee <strong>{{ $employee->employee_name ?? 'N/A' }}</strong> has submitted a new leave application. Below are the full application details for your review:
                            </p>

                            <!-- Custom Template Body (if configured) -->
                            @if(!empty($template_body))
                            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 20px; font-size: 14px; line-height: 1.6; color: #334155;">
                                {!! $template_body !!}
                            </div>
                            @endif

                            <!-- Leave Details Card -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 24px; font-size: 13px;">
                                <tr>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 600; width: 38%;">Employee Name:</td>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #0f172a; font-weight: 700;">
                                        {{ $employee->employee_name ?? 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 600;">Employee ID / Code:</td>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #0f172a; font-weight: 600;">
                                        {{ $employee->employee_id ?? 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 600;">Designation:</td>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #334155;">
                                        {{ $employee->designation?->name ?? 'Staff Member' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 600;">Agency / Department:</td>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #334155;">
                                        {{ $employee->agency?->agency_name ?? 'In-House / Direct' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 600;">Leave Type:</td>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #2563eb; font-weight: 700;">
                                        {{ $leaveType->leave_name ?? 'General Leave' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 600;">Leave Period:</td>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #0f172a; font-weight: 700;">
                                        {{ \Carbon\Carbon::parse($leave->from_date)->format('d M, Y') }} &nbsp;to&nbsp; {{ \Carbon\Carbon::parse($leave->to_date)->format('d M, Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 600;">Total Duration:</td>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #0f172a; font-weight: 700;">
                                        {{ $leave->duration ?: (\Carbon\Carbon::parse($leave->from_date)->diffInDays(\Carbon\Carbon::parse($leave->to_date)) + 1) }} Day(s)
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 16px; color: #64748b; font-weight: 600; vertical-align: top;">Reason:</td>
                                    <td style="padding: 12px 16px; color: #334155; line-height: 1.5;">
                                        {{ $leave->reason ?: 'No reason provided' }}
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA Buttons / Notice -->
                            <p style="font-size: 13px; line-height: 1.5; color: #64748b; margin-bottom: 20px;">
                                You can review, approve, or reject this request directly via the <strong>Supervisor Mobile App</strong> or through the <strong>EMS Web Portal</strong>.
                            </p>

                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="border-radius: 6px; background-color: #2563eb;">
                                        <a href="{{ url('/attendance/leave/request') }}" target="_blank" style="display: inline-block; padding: 12px 24px; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 600; border-radius: 6px;">
                                            Open EMS Portal &rarr;
                                        </a>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 30px; text-align: center; color: #64748b; font-size: 12px; line-height: 1.6;">
                            <strong>{{ $companyDetails->company_name ?? 'VProtect EMS Portal' }}</strong><br>
                            {{ $companyDetails->address ?? '' }}<br>
                            <span style="color: #94a3b8; font-size: 11px;">This is an automated notification from VProtect EMS. Please do not reply directly to this email.</span>
                        </td>
                    </tr>

                </table>
                <!-- End Main Container -->

            </td>
        </tr>
    </table>

</body>
</html>
