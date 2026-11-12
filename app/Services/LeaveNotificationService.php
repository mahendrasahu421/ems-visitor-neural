<?php

namespace App\Services;

use App\Models\Leave;
use App\Models\Employee;
use App\Models\User;
use App\Models\EmailTemplate;
use App\Mail\LeaveAppliedMail;
use App\Mail\LeaveStatusMail;
use App\Mail\LeaveUnderReviewMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class LeaveNotificationService
{
    /**
     * Send email notifications when an employee applies for leave.
     * Recipient: Supervisor (if assigned) and Admin(s).
     */
    public static function notifyLeaveApplied(Leave $leave): bool
    {
        try {
            $leave->loadMissing(['employee.designation', 'employee.agency', 'leaveType']);
            $employee = $leave->employee;

            if (!$employee) {
                Log::warning("[LeaveMail] Cannot send applied notification: Leave #{$leave->id} has no employee.");
                return false;
            }

            // 1. Resolve Supervisor
            $supervisorEmail = null;
            $supervisorName = null;

            if (!empty($employee->supervisor_id)) {
                $supervisorEmp = Employee::find($employee->supervisor_id);
                if ($supervisorEmp && !empty($supervisorEmp->employee_email)) {
                    $supervisorEmail = trim($supervisorEmp->employee_email);
                    $supervisorName = $supervisorEmp->employee_name;
                } else {
                    $supervisorUser = User::find($employee->supervisor_id);
                    if ($supervisorUser && !empty($supervisorUser->email)) {
                        $supervisorEmail = trim($supervisorUser->email);
                        $supervisorName = $supervisorUser->name;
                    }
                }
            }

            // 2. Resolve Admin / HR Emails
            $adminEmails = User::whereIn('role_id', [1, 2]) // Super Admin & HR/Admin
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->pluck('email')
                ->map(fn ($e) => trim($e))
                ->filter(fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
                ->unique()
                ->values()
                ->toArray();

            // Default fallback if no admin found in DB
            $fallbackEmail = config('mail.from.address', 'support.vprotectindia@gmail.com');
            if (empty($adminEmails) && empty($supervisorEmail)) {
                $adminEmails = [$fallbackEmail];
            }

            // 3. Determine 'To' and 'Cc' recipients
            $toRecipient = null;
            $ccRecipients = [];
            $recipientRole = 'Supervisor';

            if (!empty($supervisorEmail) && filter_var($supervisorEmail, FILTER_VALIDATE_EMAIL)) {
                $toRecipient = $supervisorEmail;
                $recipientRole = 'Supervisor';
                // CC all admins who are not the supervisor themselves
                $ccRecipients = array_values(array_filter($adminEmails, fn ($e) => strtolower($e) !== strtolower($supervisorEmail)));
            } else {
                $recipientRole = 'Admin / HR';
                if (!empty($adminEmails)) {
                    $toRecipient = array_shift($adminEmails);
                    $ccRecipients = $adminEmails;
                } else {
                    $toRecipient = $fallbackEmail;
                }
            }

            // 4. Retrieve Email Template for Supervisor/Senior
            $supervisorTemplate = EmailTemplate::where('template_key', 'leave_request')->first();

            // MAIL 1: Send to Reporting Senior / Supervisor (CC: Admins)
            $mailableSupervisor = new LeaveAppliedMail($leave, $recipientRole, $supervisorName, $supervisorTemplate);

            $mailQuery = Mail::to($toRecipient);
            if (!empty($ccRecipients)) {
                $mailQuery->cc($ccRecipients);
            }
            $mailQuery->send($mailableSupervisor);

            Log::info("[LeaveMail] Leave Applied notification sent to {$recipientRole} ({$toRecipient}) for Leave #{$leave->id} (CC: " . implode(', ', $ccRecipients) . ")");

            // MAIL 2: Send Confirmation / Under Review Email to the Employee
            $employeeEmail = trim($employee->employee_email ?? '');
            if (!empty($employeeEmail) && filter_var($employeeEmail, FILTER_VALIDATE_EMAIL)) {
                try {
                    $employeeTemplate = EmailTemplate::where('template_key', 'leave_under_review')->first();
                    $mailableEmployee = new LeaveUnderReviewMail($leave, $supervisorName, $employeeTemplate);
                    Mail::to($employeeEmail)->send($mailableEmployee);
                    Log::info("[LeaveMail] Leave Under Review confirmation sent to employee ({$employeeEmail}) for Leave #{$leave->id}");
                } catch (\Throwable $empMailErr) {
                    Log::warning("[LeaveMail] Failed sending employee under review email for Leave #{$leave->id}: " . $empMailErr->getMessage());
                }
            }

            $leaveTypeName = $leave->leaveType->leave_name ?? 'Leave';

            // Send Push Notification to Supervisor
            if (!empty($employee->supervisor_id)) {
                try {
                    app(FirebaseNotificationService::class)->sendToEmployee(
                        (int) $employee->supervisor_id,
                        'New Leave Request',
                        "{$employee->employee_name} applied for {$leaveTypeName} ({$leave->duration} days).",
                        [
                            'type' => 'leave_applied',
                            'leave_id' => $leave->id,
                            'employee_id' => $employee->employee_id,
                            'employee_name' => $employee->employee_name,
                        ],
                        [
                            'sender_type' => 'employee',
                            'sender_id' => $employee->employee_id,
                        ]
                    );
                } catch (\Throwable $pushErr) {
                    Log::warning("[FCM] Leave applied supervisor push failed: " . $pushErr->getMessage());
                }
            }

            // Send Push Notification to Employee (Under Review confirmation)
            try {
                app(FirebaseNotificationService::class)->sendToEmployee(
                    (int) $employee->employee_id,
                    'Leave Request Submitted',
                    "Your {$leaveTypeName} request ({$leave->duration} days) has been submitted and is under review.",
                    [
                        'type' => 'leave_under_review',
                        'leave_id' => $leave->id,
                        'status' => 'PENDING',
                    ],
                    [
                        'sender_type' => 'system',
                    ]
                );
            } catch (\Throwable $pushErr) {
                Log::warning("[FCM] Leave applied employee push failed: " . $pushErr->getMessage());
            }

            return true;
        } catch (\Throwable $e) {
            Log::error("[LeaveMail] Failed to send Leave Applied notification for Leave #{$leave->id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Send email notification to employee when their leave is approved or rejected.
     */
    public static function notifyLeaveStatusChanged(Leave $leave, string $status, ?string $actorName = null, ?string $remarks = null): bool
    {
        try {
            $leave->loadMissing(['employee.designation', 'leaveType']);
            $employee = $leave->employee;

            if (!$employee || empty($employee->employee_email)) {
                Log::warning("[LeaveMail] Cannot send status notification: Employee or employee_email missing for Leave #{$leave->id}.");
                return false;
            }

            $employeeEmail = trim($employee->employee_email);
            if (!filter_var($employeeEmail, FILTER_VALIDATE_EMAIL)) {
                Log::warning("[LeaveMail] Invalid employee email '{$employeeEmail}' for Leave #{$leave->id}.");
                return false;
            }

            $normalizedStatus = strtoupper(trim($status)) === 'APPROVED' ? 'APPROVED' : 'REJECTED';
            $templateKey = $normalizedStatus === 'APPROVED' ? 'leave_approved' : 'leave_rejected';

            $template = EmailTemplate::where('template_key', $templateKey)->first();

            $mailable = new LeaveStatusMail($leave, $normalizedStatus, $actorName, $remarks, $template);

            Mail::to($employeeEmail)->send($mailable);

            Log::info("[LeaveMail] Leave Status ({$normalizedStatus}) notification sent for Leave #{$leave->id} to employee: {$employeeEmail}");

            // Send Push Notification to Employee
            try {
                $statusWord = $normalizedStatus === 'APPROVED' ? 'Approved' : 'Rejected';
                $leaveTypeName = $leave->leaveType->leave_name ?? 'Leave';
                app(FirebaseNotificationService::class)->sendToEmployee(
                    (int) $employee->employee_id,
                    "Leave Request {$statusWord}",
                    "Your {$leaveTypeName} request ({$leave->duration} days) has been {$statusWord}." . ($remarks ? " Note: {$remarks}" : ""),
                    [
                        'type' => 'leave_status',
                        'leave_id' => $leave->id,
                        'status' => $normalizedStatus,
                        'remarks' => $remarks,
                    ],
                    [
                        'sender_type' => 'system',
                    ]
                );
            } catch (\Throwable $pushErr) {
                Log::warning("[FCM] Leave status push failed: " . $pushErr->getMessage());
            }

            return true;
        } catch (\Throwable $e) {
            Log::error("[LeaveMail] Failed to send Leave Status notification for Leave #{$leave->id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }
}
