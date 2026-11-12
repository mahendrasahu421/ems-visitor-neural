<?php

namespace App\Mail;

use App\Models\Leave;
use App\Models\Company;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class LeaveAppliedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leave;
    public $employee;
    public $leaveType;
    public $recipientRole;
    public $supervisorName;
    public $subjectLine;
    public $templateBody;
    public $companyDetails;

    public function __construct(Leave $leave, ?string $recipientRole = 'Supervisor', ?string $supervisorName = null, ?EmailTemplate $template = null)
    {
        $this->leave = $leave;
        $this->employee = $leave->employee;
        $this->leaveType = $leave->leaveType;
        $this->recipientRole = $recipientRole ?? 'Supervisor';
        $this->supervisorName = $supervisorName;
        $this->companyDetails = Company::first();

        $fromDate = Carbon::parse($leave->from_date)->format('d M, Y');
        $toDate = Carbon::parse($leave->to_date)->format('d M, Y');
        $duration = $leave->duration ?: (Carbon::parse($leave->from_date)->diffInDays(Carbon::parse($leave->to_date)) + 1);
        $empName = $this->employee?->employee_name ?? 'Employee';
        $empId = $this->employee?->employee_id ?? 'N/A';
        $typeName = $this->leaveType?->leave_name ?? 'Leave';

        $dayWord = $duration > 1 ? 'days' : 'day';
        $defaultSubject = "New Leave Request: {$empName} ({$typeName} - {$duration} {$dayWord})";

        if ($template && !empty($template->subject) && !preg_match('/\b(approved|rejected)\b/i', $template->subject)) {
            $this->subjectLine = strtr($template->subject, [
                '{employee_name}' => $empName,
                '{employee_id}'   => $empId,
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
                '{recipient_name}' => $supervisorName ?: 'Sir/Madam',
                '{employee_name}'  => $empName,
                '{employee_id}'    => $empId,
                '{leave_type}'     => $typeName,
                '{from_date}'      => $fromDate,
                '{to_date}'        => $toDate,
                '{days}'           => $duration,
                '{reason}'         => nl2br(e($leave->reason ?: 'No reason provided')),
            ]);
        } else {
            $this->templateBody = null;
        }
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
            ->view('emails.leave_applied')
            ->with([
                'leave'          => $this->leave,
                'employee'       => $this->employee,
                'leaveType'      => $this->leaveType,
                'subject'        => $this->subjectLine,
                'recipientRole'  => $this->recipientRole,
                'supervisorName' => $this->supervisorName,
                'template_body'  => $this->templateBody,
                'companyDetails' => $this->companyDetails,
            ]);
    }
}