<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClientCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $recipientName,
        public string $loginEmail,
        public string $rawPassword,
        public string $projectName,
        public ?string $clientCompanyName = null,
        public ?string $designation = null
    ) {
    }

    public function build()
    {
        $subject = 'Your Client Portal Access Credentials - ' . $this->projectName;

        return $this->subject($subject)
            ->view('emails.client-credentials');
    }
}
