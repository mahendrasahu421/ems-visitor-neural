<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\EmailTemplate;
use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveUnderReviewMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leave;
    public $employee;
    public $leaveType;
    public $supervisorName;
    public $subjectLine;
    public $templateBody;
    public $companyDetails;

    public function __construct(Leave $leave, ?string $supervisorName = null, ?EmailTemplate $template = null)
    {
        $this->leave = $leave;
        $this->employee = $leave->employee;
        $this->leaveType = $leave->leaveType;
        $this->supervisorName = $supervisorName ?: 'Reporting Supervisor';
        $this->companyDetails = Company::first();

        $fromDate = Carbon::parse($leave->from_date)->format('d M, Y');
        $toDate = Carbon::parse($leave->to_date)->format('d M, Y');
        $duration = $leave->duration ?: (Carbon::parse($leave->from_date)->diffInDays(Carbon::parse($leave->to_date)) + 1);
        $dayWord = $duration > 1 ? 'days' : 'day';
        $empName = $this->employee?->employee_name ?? 'Employee';
        $typeName = $this->leaveType?->leave_name ?? 'Leave';

        $defaultSubject = "Leave Application Submitted - Under Review: {$typeName} ({$fromDate} to {$toDate})";

        if ($template && !empty($template->subject) && !preg_match('/\b(approved|rejected)\b/i', $template->subject)) {
            $this->subjectLine = strtr($template->subject, [
                '{employee_name}'  => $empName,
                '{leave_type}'     => $typeName,
                '{from_date}'      => $fromDate,
                '{to_date}'        => $toDate,
                '{days}'           => $duration,
                '{supervisor_name}'=> $this->supervisorName,
            ]);
        } else {
            $this->subjectLine = $defaultSubject;
        }

        if ($template && !empty($template->body)) {
            $this->templateBody = strtr($template->body, [
                '{employee_name}'  => $empName,
                '{leave_type}'     => $typeName,
                '{from_date}'      => $fromDate,
                '{to_date}'        => $toDate,
                '{days}'           => $duration,
                '{supervisor_name}'=> $this->supervisorName,
                '{reason}'         => nl2br(e($leave->reason ?: 'No reason provided')),
            ]);
        } else {
            $this->templateBody = null;
        }
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
            ->view('emails.leave_under_review')
            ->with([
                'leave'          => $this->leave,
                'employee'       => $this->employee,
                'leaveType'      => $this->leaveType,
                'subject'        => $this->subjectLine,
                'supervisorName' => $this->supervisorName,
                'template_body'  => $this->templateBody,
                'companyDetails' => $this->companyDetails,
            ]);
    }
}
