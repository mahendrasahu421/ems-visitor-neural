<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class DailyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reports;
    public $userName;

    public function __construct($reports)
    {
        $this->reports = $reports;
        $this->userName = Auth::guard('employee')->user()->employee_name;
    }

    public function build()
    {
        return $this->subject('Daily Report Submitted - ' . $this->userName)
            ->view('emails.daily-report');
    }
}
