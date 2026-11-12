<?php

namespace App\Mail;

use App\Models\ProjectTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectTicketNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ProjectTicket $ticket,
        public string $type = 'created'
    ) {
    }

    public function build()
    {
        $subjectPrefix = match ($this->type) {
            'assigned' => 'Ticket Assigned',
            'employee_response' => 'Ticket Response Updated',
            default => 'New Client Ticket',
        };

        return $this->subject($subjectPrefix . ' - ' . $this->ticket->ticket_no)
            ->view('emails.project-ticket-notification');
    }
}
