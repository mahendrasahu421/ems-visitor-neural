<?php

namespace App\Services;

use App\Mail\ResignationDecisionMail;
use App\Mail\ResignationSubmittedMail;
use App\Models\EmployeeResignation;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ResignationNotificationService
{
    /**
     * Dispatch submission notifications to Supervisor, Admins, and custom CC recipients.
     */
    public function sendSubmissionNotification(EmployeeResignation $resignation): void
    {
        try {
            $employee = $resignation->employee;
            $supervisor = $resignation->supervisor;
            $ccEmails = $resignation->clean_cc_emails;

            // 1. Gather Admin Emails
            $adminEmails = User::whereIn('role_id', [1, 2])
                ->whereNotNull('email')
                ->pluck('email')
                ->filter()
                ->toArray();

            if (empty($adminEmails)) {
                $fallback = config('mail.from.address');
                if ($fallback) {
                    $adminEmails = [$fallback];
                }
            }

            // 2. Identify Primary Recipients & CC
            $primaryRecipients = [];

            if ($resignation->initiated_by === 'employee') {
                // Sent to Supervisor first
                if ($supervisor && !empty($supervisor->employee_email)) {
                    $primaryRecipients[] = $supervisor->employee_email;
                }
                // Also send to Admins
                foreach ($adminEmails as $admEmail) {
                    if (!in_array($admEmail, $primaryRecipients, true)) {
                        $primaryRecipients[] = $admEmail;
                    }
                }
            } else {
                // Initiated by Supervisor or Admin: Sent to Employee
                if ($employee && !empty($employee->employee_email)) {
                    $primaryRecipients[] = $employee->employee_email;
                }
                // And Admins
                foreach ($adminEmails as $admEmail) {
                    if (!in_array($admEmail, $primaryRecipients, true)) {
                        $primaryRecipients[] = $admEmail;
                    }
                }
            }

            // Filter out empty and invalid
            $validPrimaries = array_values(array_filter($primaryRecipients, fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL)));
            $validCcs = array_values(array_filter($ccEmails, fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL) && !in_array($e, $validPrimaries, true)));

            if (empty($validPrimaries) && !empty($validCcs)) {
                $validPrimaries = [array_shift($validCcs)];
            }

            if (!empty($validPrimaries)) {
                $primary = array_shift($validPrimaries);
                $mailable = new ResignationSubmittedMail($resignation);

                $pendingMail = Mail::to($primary);

                // Add other primaries as CC
                $allCcs = array_unique(array_merge($validPrimaries, $validCcs));
                if (!empty($allCcs)) {
                    $pendingMail->cc($allCcs);
                }

                $pendingMail->send($mailable);
            }
        } catch (\Throwable $e) {
            Log::error('Resignation submission email failed: ' . $e->getMessage(), [
                'resignation_id' => $resignation->id,
                'trace'          => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Dispatch decision notification (Accepted / Rejected) to Employee, Supervisor, and CCs.
     */
    public function sendDecisionNotification(EmployeeResignation $resignation): void
    {
        try {
            $employee = $resignation->employee;
            $supervisor = $resignation->supervisor;
            $ccEmails = $resignation->clean_cc_emails;

            if (!$employee || empty($employee->employee_email)) {
                return;
            }

            $primary = $employee->employee_email;
            $additionalCcs = [];

            if ($supervisor && !empty($supervisor->employee_email) && $supervisor->employee_email !== $primary) {
                $additionalCcs[] = $supervisor->employee_email;
            }

            $adminEmails = User::whereIn('role_id', [1, 2])
                ->whereNotNull('email')
                ->pluck('email')
                ->filter()
                ->toArray();

            foreach ($adminEmails as $adm) {
                if ($adm !== $primary && !in_array($adm, $additionalCcs, true)) {
                    $additionalCcs[] = $adm;
                }
            }

            foreach ($ccEmails as $cc) {
                if ($cc !== $primary && !in_array($cc, $additionalCcs, true)) {
                    $additionalCcs[] = $cc;
                }
            }

            $mailable = new ResignationDecisionMail($resignation);
            $pendingMail = Mail::to($primary);

            if (!empty($additionalCcs)) {
                $pendingMail->cc($additionalCcs);
            }

            $pendingMail->send($mailable);
        } catch (\Throwable $e) {
            Log::error('Resignation decision email failed: ' . $e->getMessage(), [
                'resignation_id' => $resignation->id,
                'trace'          => $e->getTraceAsString(),
            ]);
        }
    }
}
