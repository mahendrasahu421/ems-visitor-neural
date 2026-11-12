<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\EmployeeResignation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResignationSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resignation;
    public $employee;
    public $supervisor;
    public $companyDetails;
    public $recipientRole;
    public $acceptUrl;
    public $rejectUrl;
    public $viewUrl;

    public function __construct(EmployeeResignation $resignation, string $recipientRole = 'supervisor')
    {
        $this->resignation = $resignation;
        $this->employee = $resignation->employee;
        $this->supervisor = $resignation->supervisor;
        $this->companyDetails = Company::first();
        $this->recipientRole = $recipientRole;

        $baseUrl = url('/');
        $this->acceptUrl = url('/resignation/action/' . $resignation->decision_token . '/accept');
        $this->rejectUrl = url('/resignation/action/' . $resignation->decision_token . '/reject');
        $this->viewUrl = url('/resignation/action/' . $resignation->decision_token . '/view');
    }

    public function build()
    {
        $empName = $this->employee?->employee_name ?? 'Employee';
        $code = $this->resignation->resignation_code;
        $actionWord = $this->resignation->initiated_by === 'employee' ? 'Resignation Submitted' : 'Exit / Separation Notice Initiated';

        return $this->subject("[{$actionWord}] {$empName} ({$code})")
            ->view('emails.resignation_submitted');
    }
}
