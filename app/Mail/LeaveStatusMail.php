<?php

namespace App\Mail;

use App\Models\Leave;
use App\Models\Company;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class LeaveStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leave;
    public $employee;
    public $leaveType;
    public $status;
    public $actorName;
    public $remarks;
    public $subjectLine;
    public $templateBody;
    public $companyDetails;

    public function __construct(Leave $leave, string $status, ?string $actorName = null, ?string $remarks = null, ?EmailTemplate $template = null)
    {
        $this->leave = $leave;
        $this->employee = $leave->employee;
        $this->leaveType = $leave->leaveType;
        $this->status = strtoupper(trim($status)) === 'APPROVED' ? 'APPROVED' : 'REJECTED';
        $this->actorName = $actorName ?: 'Management';
        $this->remarks = $remarks ?: ($leave->supervisor_remarks ?: null);
        $this->companyDetails = Company::first();

        $fromDate = Carbon::parse($leave->from_date)->format('d M, Y');
        $toDate = Carbon::parse($leave->to_date)->format('d M, Y');
        $duration = $leave->duration ?: (Carbon::parse($leave->from_date)->diffInDays(Carbon::parse($leave->to_date)) + 1);
        $empName = $this->employee?->employee_name ?? 'Employee';
        $typeName = $this->leaveType?->leave_name ?? 'Leave';
        $statusText = $this->status === 'APPROVED' ? 'Approved' : 'Rejected';

        $defaultSubject = "Leave Request {$statusText}: {$typeName} ({$fromDate} to {$toDate})";

        if ($template && !empty($template->subject)) {
            $this->subjectLine = strtr($template->subject, [
                '{employee_name}' => $empName,
                '{status}'        => $statusText,
                '{leave_type}'    => $typeName,
                '{from_date}'     => $fromDate,
                '{to_date}'       => $toDate,
                '{days}'          => $duration,
            ]);
        } else {
            $this->subjectLine = $defaultSubject;
        }

        if ($template && !empty($template->body)) {
            $this->templateBody = strtr($template->body, [
                '{employee_name}' => $empName,
                '{status}'        => $statusText,
                '{leave_type}'    => $typeName,
                '{from_date}'     => $fromDate,
                '{to_date}'       => $toDate,
                '{days}'          => $duration,
                '{start_date}'    => $fromDate,
                '{end_date}'      => $toDate,
                '{actor_name}'    => $this->actorName,
                '{remarks}'       => nl2br(e($this->remarks ?: 'None')),
            ]);
        } else {
            $this->templateBody = null;
        }
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
            ->view('emails.leave_status')
            ->with([
                'leave'          => $this->leave,
                'employee'       => $this->employee,
                'leaveType'      => $this->leaveType,
                'status'         => $this->status,
                'actorName'      => $this->actorName,
                'remarks'        => $this->remarks,
                'subject'        => $this->subjectLine,
                'template_body'  => $this->templateBody,
                'companyDetails' => $this->companyDetails,
            ]);
    }
}
