<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your Client Portal Access Credentials</title>
</head>
<body style="font-family: 'Segoe UI', Arial, sans-serif; color: #333333; background-color: #f4f6f9; margin: 0; padding: 25px 0;">
    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
        <!-- Header -->
        <tr>
            <td style="background: linear-gradient(135deg, #0d6efd 0%, #4338ca 100%); padding: 30px; text-align: center; color: #ffffff;">
                <h1 style="margin: 0; font-size: 22px; font-weight: 700; letter-spacing: 0.5px;">Client Portal Access</h1>
                <p style="margin: 6px 0 0; font-size: 14px; opacity: 0.9;">Credentials for {{ $projectName }}</p>
            </td>
        </tr>

        <!-- Body -->
        <tr>
            <td style="padding: 30px 25px;">
                <p style="font-size: 15px; margin-top: 0; color: #1e293b;">Dear <strong>{{ $recipientName }}</strong>,</p>
                <p style="font-size: 14px; line-height: 1.6; color: #475569;">
                    An account has been created for you on the Client Portal for project <strong>{{ $projectName }}</strong>
                    @if (!empty($clientCompanyName)) ({{ $clientCompanyName }}) @endif.
                    You can log in to track project progress, monitor milestones, review invoices, and raise support tickets.
                </p>

                <!-- Credentials Card -->
                <div style="background-color: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; padding: 20px; margin: 25px 0;">
                    <h3 style="margin: 0 0 15px; font-size: 14px; text-transform: uppercase; color: #0d6efd; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
                        Your Login Credentials
                    </h3>
                    <table cellpadding="6" cellspacing="0" border="0" width="100%" style="font-size: 14px;">
                        <tr>
                            <td width="35%" style="color: #64748b;"><strong>Portal URL:</strong></td>
                            <td width="65%"><a href="{{ url('/client/login') }}" style="color: #0d6efd; text-decoration: none; font-weight: 600;">{{ url('/client/login') }}</a></td>
                        </tr>
                        <tr>
                            <td style="color: #64748b;"><strong>Login Email:</strong></td>
                            <td style="color: #0f172a; font-weight: 600; font-family: monospace; font-size: 14px;">{{ $loginEmail }}</td>
                        </tr>
                        <tr>
                            <td style="color: #64748b;"><strong>Password:</strong></td>
                            <td style="color: #0f172a; font-weight: 700; font-family: monospace; font-size: 15px; background: #e2e8f0; padding: 3px 8px; border-radius: 4px; display: inline-block;">{{ $rawPassword }}</td>
                        </tr>
                        @if (!empty($designation))
                        <tr>
                            <td style="color: #64748b;"><strong>Role / Designation:</strong></td>
                            <td style="color: #334155;">{{ $designation }}</td>
                        </tr>
                        @endif
                    </table>
                </div>

                <!-- CTA Button -->
                <div style="text-align: center; margin: 30px 0 20px;">
                    <a href="{{ url('/client/login') }}" style="background-color: #0d6efd; color: #ffffff; padding: 12px 28px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; display: inline-block; box-shadow: 0 3px 6px rgba(13,110,253,0.25);">
                        Log In to Client Portal &rarr;
                    </a>
                </div>

                <!-- Security Note -->
                <div style="background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 12px 15px; border-radius: 6px; margin-top: 25px;">
                    <p style="margin: 0; font-size: 12px; color: #92400e; line-height: 1.5;">
                        <strong>Security Note:</strong> Please keep your login credentials secure. For your safety, we recommend changing your password after your first login.
                    </p>
                </div>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 18px 25px; text-align: center; font-size: 12px; color: #94a3b8;">
                This is an automated notification from the Project Management System. Please do not reply directly to this email.
            </td>
        </tr>
    </table>
</body>
</html>
