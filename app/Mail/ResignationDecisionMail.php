<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\EmployeeResignation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResignationDecisionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resignation;
    public $employee;
    public $supervisor;
    public $companyDetails;
    public $decision; // 'approved' or 'rejected'
    public $remarks;
    public $approvedLwd;

    public function __construct(EmployeeResignation $resignation)
    {
        $this->resignation = $resignation;
        $this->employee = $resignation->employee;
        $this->supervisor = $resignation->supervisor;
        $this->companyDetails = Company::first();
        $this->decision = $resignation->status;
        $this->remarks = $resignation->action_remarks;
        $this->approvedLwd = $resignation->effectiveLastWorkingDay();
    }

    public function build()
    {
        $empName = $this->employee?->employee_name ?? 'Employee';
        $code = $this->resignation->resignation_code;
        $decisionText = $this->decision === 'approved' ? 'Accepted / Approved' : 'Rejected';

        return $this->subject("[Resignation {$decisionText}] {$empName} ({$code})")
            ->view('emails.resignation_decision');
    }
}
