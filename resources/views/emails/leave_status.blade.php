<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Leave Request Status Update' }}</title>
</head>
@php
    $isApproved = strtoupper($status) === 'APPROVED';
    $themeColor = $isApproved ? '#16a34a' : '#dc2626';
    $headerBg = $isApproved ? 'linear-gradient(135deg, #15803d 0%, #16a34a 100%)' : 'linear-gradient(135deg, #b91c1c 0%, #dc2626 100%)';
    $badgeBg = $isApproved ? '#dcfce7' : '#fee2e2';
    $badgeText = $isApproved ? '#166534' : '#991b1b';
    $statusTitle = $isApproved ? 'Leave Request Approved' : 'Leave Request Rejected';
@endphp
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; -webkit-font-smoothing: antialiased;">

    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; padding: 30px 15px;">
        <tr>
            <td align="center">
                
                <!-- Main Container -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); border: 1px solid #e2e8f0;">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: {{ $headerBg }}; padding: 25px 30px; text-align: left;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="vertical-align: middle;">
                                        <div style="color: #ffffff; opacity: 0.85; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">
                                            VProtect EMS &bull; Leave Status Update
                                        </div>
                                        <h1 style="color: #ffffff; font-size: 20px; font-weight: 700; margin: 0; line-height: 1.3;">
                                            {{ $statusTitle }}
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

                    <!-- Status Notification Banner -->
                    <tr>
                        <td style="background-color: {{ $badgeBg }}; border-bottom: 1px solid #e2e8f0; padding: 12px 30px;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="color: {{ $badgeText }}; font-size: 13px; font-weight: 700;">
                                        @if($isApproved)
                                            &#10004; Your leave request has been APPROVED.
                                        @else
                                            &#10006; Your leave request has been REJECTED.
                                        @endif
                                    </td>
                                    <td align="right">
                                        <span style="background-color: {{ $themeColor }}; color: #ffffff; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.5px;">
                                            {{ $isApproved ? 'APPROVED' : 'REJECTED' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 25px 30px;">
                            
                            <p style="font-size: 14px; line-height: 1.6; color: #334155; margin-top: 0; margin-bottom: 15px;">
                                Dear <strong>{{ $employee->employee_name ?? 'Employee' }}</strong>,
                            </p>
                            
                            <p style="font-size: 14px; line-height: 1.6; color: #334155; margin-bottom: 20px;">
                                @if($isApproved)
                                    We are pleased to inform you that your leave application has been <strong>approved</strong> by <strong>{{ $actorName ?: 'Management' }}</strong>.
                                @else
                                    This is to inform you that your leave application has been <strong>reviewed and rejected</strong> by <strong>{{ $actorName ?: 'Management' }}</strong>.
                                @endif
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
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 600; width: 38%;">Leave Type:</td>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #0f172a; font-weight: 700;">
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
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 600;">Action Taken By:</td>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #334155; font-weight: 600;">
                                        {{ $actorName ?: 'Supervisor / HR' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 600;">Original Reason:</td>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #334155;">
                                        {{ $leave->reason ?: 'No reason provided' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 16px; color: #64748b; font-weight: 600; vertical-align: top;">
                                        {{ $isApproved ? 'Supervisor Remarks:' : 'Rejection Reason / Remarks:' }}
                                    </td>
                                    <td style="padding: 12px 16px; color: {{ $isApproved ? '#334155' : '#b91c1c' }}; font-weight: 600; line-height: 1.5;">
                                        {{ $remarks ?: ($isApproved ? 'Approved by management.' : 'No remarks specified.') }}
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size: 13px; line-height: 1.5; color: #64748b; margin-bottom: 20px;">
                                You can check your updated leave balance and attendance history anytime via the <strong>Employee Mobile App</strong> or <strong>Web Portal</strong>.
                            </p>

                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="border-radius: 6px; background-color: {{ $themeColor }};">
                                        <a href="{{ url('/employee/leaves') }}" target="_blank" style="display: inline-block; padding: 12px 24px; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 600; border-radius: 6px;">
                                            View My Leaves &rarr;
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
