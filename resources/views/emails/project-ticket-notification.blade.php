<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $type === 'assigned' ? 'Ticket Assigned' : ($type === 'employee_response' ? 'Ticket Response Updated' : 'New Client Ticket') }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #222; line-height: 1.5;">
    <h2 style="margin-bottom: 8px;">{{ $type === 'assigned' ? 'Ticket Assigned' : ($type === 'employee_response' ? 'Ticket Response Updated' : 'New Client Ticket Raised') }}</h2>
    <p style="margin-top: 0;">Ticket <strong>{{ $ticket->ticket_no }}</strong> needs attention.</p>

    <table cellpadding="8" cellspacing="0" border="0" style="border-collapse: collapse; width: 100%; max-width: 680px;">
        <tr>
            <td style="border: 1px solid #ddd; width: 160px;"><strong>Client</strong></td>
            <td style="border: 1px solid #ddd;">{{ $ticket->client?->full_name ?: $ticket->client?->company_name }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #ddd;"><strong>Project</strong></td>
            <td style="border: 1px solid #ddd;">{{ $ticket->project?->project_name ?? $ticket->project_name ?? 'General' }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #ddd;"><strong>Subject</strong></td>
            <td style="border: 1px solid #ddd;">{{ $ticket->subject }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #ddd;"><strong>Priority</strong></td>
            <td style="border: 1px solid #ddd;">{{ $ticket->priority }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #ddd;"><strong>Status</strong></td>
            <td style="border: 1px solid #ddd;">{{ $ticket->status }}</td>
        </tr>
        @if ($ticket->employee_status)
            <tr>
                <td style="border: 1px solid #ddd;"><strong>Employee Status</strong></td>
                <td style="border: 1px solid #ddd;">{{ $ticket->employee_status }}</td>
            </tr>
        @endif
        @if ($ticket->assignedEmployee)
            <tr>
                <td style="border: 1px solid #ddd;"><strong>Assigned To</strong></td>
                <td style="border: 1px solid #ddd;">{{ $ticket->assignedEmployee->employee_name }}</td>
            </tr>
        @endif
    </table>

    <h3 style="margin-bottom: 6px;">Description</h3>
    <p style="white-space: pre-line;">{{ $ticket->description }}</p>

    @if ($ticket->admin_note)
        <h3 style="margin-bottom: 6px;">Support Note</h3>
        <p style="white-space: pre-line;">{{ $ticket->admin_note }}</p>
    @endif

    @if ($ticket->employee_note)
        <h3 style="margin-bottom: 6px;">Employee Reply</h3>
        <p style="white-space: pre-line;">{{ $ticket->employee_note }}</p>
    @endif

    <p>
        <a href="{{ route('admin.tickets.show', $ticket) }}" style="color: #0d6efd;">Open ticket in admin panel</a>
    </p>
</body>
</html>
