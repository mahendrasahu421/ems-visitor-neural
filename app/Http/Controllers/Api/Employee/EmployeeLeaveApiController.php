<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\LeaveActivityLog;
use App\Models\LeaveMapping;
use App\Models\LeaveType;
use App\Models\User;
use App\Mail\LeaveAppliedMail;
use App\Services\FirebaseNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class EmployeeLeaveApiController extends Controller
{
    /**
     * Get list of active Leave Types with quota balances, usage history, and applicability.
     */
    public function types(Request $request)
    {
        $employee = $request->user();
        $year = (int) $request->input('year', Carbon::now()->year);
        $applicableOnly = filter_var($request->input('applicable_only', false), FILTER_VALIDATE_BOOLEAN);

        // Fetch leave mappings for employee's designation if assigned
        $mappingsByTypeId = collect();
        if ($employee && $employee->designation_id) {
            $mappingsByTypeId = LeaveMapping::where('designation_id', $employee->designation_id)
                ->where('status', 'Active')
                ->get()
                ->keyBy('leave_type_id');
        }

        // Fetch approved and pending leaves for the employee in the specified year
        $approvedLeavesUsed = collect();
        $pendingLeaves = collect();
        if ($employee) {
            $approvedLeavesUsed = Leave::where('employee_id', $employee->employee_id)
                ->where('status', 'APPROVED')
                ->where(function ($q) use ($year) {
                    $q->whereYear('from_date', $year)->orWhereYear('to_date', $year);
                })
                ->get()
                ->groupBy('leave_type_id');

            $pendingLeaves = Leave::where('employee_id', $employee->employee_id)
                ->whereIn('status', ['SENT', 'PENDING'])
                ->where(function ($q) use ($year) {
                    $q->whereYear('from_date', $year)->orWhereYear('to_date', $year);
                })
                ->get()
                ->groupBy('leave_type_id');
        }

        $types = LeaveType::where('status', 'Active')->orderBy('id')->get();

        $totalAllottedSummary = 0;
        $totalUsedSummary = 0;
        $totalPendingSummary = 0;
        $totalRemainingSummary = 0;

        $genderRaw = $employee ? (string) ($employee->employee_gender ?? '') : '';
        $isFemale = in_array(strtolower($genderRaw), ['2', 'female', 'f', 'woman'], true);
        $isMale = in_array(strtolower($genderRaw), ['1', 'male', 'm', 'man'], true);

        $data = [];

        foreach ($types as $t) {
            // Check applicability
            $applicableFor = trim($t->applicable_for ?? 'All');
            $isApplicable = true;
            $notApplicableReason = null;

            if (strcasecmp($applicableFor, 'Female') === 0 && !$isFemale && $genderRaw !== '') {
                $isApplicable = false;
                $notApplicableReason = 'Applicable for female employees only';
            } elseif (strcasecmp($applicableFor, 'Male') === 0 && !$isMale && $genderRaw !== '') {
                $isApplicable = false;
                $notApplicableReason = 'Applicable for male employees only';
            }

            if ($applicableOnly && !$isApplicable) {
                continue;
            }

            // Quota calculation (designation mapping takes precedence if configured)
            if ($mappingsByTypeId->has($t->id)) {
                $allottedDays = (int) $mappingsByTypeId->get($t->id)->allow_days;
            } else {
                $allottedDays = (int) ($t->total_leaves ?? 0);
            }

            $usedDays = 0;
            if ($approvedLeavesUsed->has($t->id)) {
                $usedDays = $approvedLeavesUsed->get($t->id)->sum(function ($l) {
                    if ($l->duration) {
                        return (int) $l->duration;
                    }
                    return Carbon::parse($l->from_date)->diffInDays(Carbon::parse($l->to_date)) + 1;
                });
            }

            $pendingDays = 0;
            if ($pendingLeaves->has($t->id)) {
                $pendingDays = $pendingLeaves->get($t->id)->sum(function ($l) {
                    if ($l->duration) {
                        return (int) $l->duration;
                    }
                    return Carbon::parse($l->from_date)->diffInDays(Carbon::parse($l->to_date)) + 1;
                });
            }

            $remainingDays = max($allottedDays - $usedDays, 0);
            $availableDays = max($allottedDays - $usedDays - $pendingDays, 0);

            if ($isApplicable) {
                $totalAllottedSummary += $allottedDays;
                $totalUsedSummary += $usedDays;
                $totalPendingSummary += $pendingDays;
                $totalRemainingSummary += $remainingDays;
            }

            $nameLower = strtolower($t->leave_name ?? '');
            $color = match (true) {
                str_contains($nameLower, 'casual') => '#8B5CF6',
                str_contains($nameLower, 'sick') || str_contains($nameLower, 'medical') => '#EF4444',
                str_contains($nameLower, 'earned') || str_contains($nameLower, 'annual') => '#10B981',
                str_contains($nameLower, 'maternity') => '#EC4899',
                str_contains($nameLower, 'restricted') || str_contains($nameLower, 'rh') => '#F59E0B',
                default => '#3B82F6',
            };
            $icon = match (true) {
                str_contains($nameLower, 'casual') => 'calendar-heart',
                str_contains($nameLower, 'sick') || str_contains($nameLower, 'medical') => 'activity',
                str_contains($nameLower, 'earned') || str_contains($nameLower, 'annual') => 'briefcase',
                str_contains($nameLower, 'maternity') => 'heart',
                str_contains($nameLower, 'restricted') || str_contains($nameLower, 'rh') => 'star',
                default => 'calendar',
            };

            $data[] = [
                'id' => $t->id,
                'name' => $t->leave_name,
                'leave_name' => $t->leave_name,
                'code' => $t->leave_code,
                'leave_code' => $t->leave_code,
                'type' => $t->leave_category ?? 'Paid',
                'category' => $t->leave_category ?? 'Paid',
                'leave_category' => $t->leave_category ?? 'Paid',
                'days' => $allottedDays,
                'total_leaves' => $allottedDays,
                'allotted_days' => $allottedDays,
                'used_days' => $usedDays,
                'used_leaves' => $usedDays,
                'pending_days' => $pendingDays,
                'remaining_days' => $remainingDays,
                'remaining_leaves' => $remainingDays,
                'available_days' => $availableDays,
                'carry_forward' => (bool) $t->carry_forward,
                'encashable' => (bool) $t->encashable,
                'applicable_for' => $t->applicable_for ?? 'All',
                'is_applicable' => $isApplicable,
                'not_applicable_reason' => $notApplicableReason,
                'description' => $t->description,
                'color' => $color,
                'icon' => $icon,
                'status' => $t->status,
            ];
        }

        return response()->json([
            'success' => true,
            'summary' => [
                'year' => $year,
                'total_allotted' => $totalAllottedSummary,
                'total_used' => $totalUsedSummary,
                'total_pending' => $totalPendingSummary,
                'total_remaining' => $totalRemainingSummary,
                'total_available' => max($totalRemainingSummary - $totalPendingSummary, 0),
            ],
            'data' => $data,
        ], 200);
    }

    /**
     * Apply for a new Leave.
     */
    public function apply(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'from_date' => 'required|date_format:Y-m-d',
            'to_date' => 'required|date_format:Y-m-d|after_or_equal:from_date',
            'reason' => 'required|string|max:2000',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120', // 5MB max
        ]);

        $employee = $request->user();
        if (!$employee->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Your employee account is currently inactive. You cannot apply for leaves.',
            ], 403);
        }
        $fromDate = Carbon::parse($request->input('from_date'));
        $toDate = Carbon::parse($request->input('to_date'));
        $duration = $fromDate->diffInDays($toDate) + 1;

        // Fetch leave type
        $leaveType = LeaveType::where('id', $request->input('leave_type_id'))
            ->where('status', 'Active')
            ->first();

        if (!$leaveType) {
            return response()->json([
                'success' => false,
                'message' => 'Selected leave type is not active or invalid.',
            ], 422);
        }

        // Check gender applicability
        $genderRaw = (string) ($employee->employee_gender ?? '');
        $isFemale = in_array(strtolower($genderRaw), ['2', 'female', 'f', 'woman'], true);
        $isMale = in_array(strtolower($genderRaw), ['1', 'male', 'm', 'man'], true);

        if (strcasecmp($leaveType->applicable_for, 'Female') === 0 && !$isFemale && $genderRaw !== '') {
            return response()->json([
                'success' => false,
                'message' => "The leave type '{$leaveType->leave_name}' is only applicable for female employees.",
            ], 422);
        }

        if (strcasecmp($leaveType->applicable_for, 'Male') === 0 && !$isMale && $genderRaw !== '') {
            return response()->json([
                'success' => false,
                'message' => "The leave type '{$leaveType->leave_name}' is only applicable for male employees.",
            ], 422);
        }

        // Check leave quota / remaining balance
        $mapping = null;
        if ($employee->designation_id) {
            $mapping = LeaveMapping::where('designation_id', $employee->designation_id)
                ->where('leave_type_id', $leaveType->id)
                ->where('status', 'Active')
                ->first();
        }

        $allotted = $mapping ? (int) $mapping->allow_days : (int) ($leaveType->total_leaves ?? 0);
        if ($allotted > 0) {
            $used = Leave::where('employee_id', $employee->employee_id)
                ->where('leave_type_id', $leaveType->id)
                ->where('status', 'APPROVED')
                ->where(function ($q) use ($fromDate) {
                    $q->whereYear('from_date', $fromDate->year)->orWhereYear('to_date', $fromDate->year);
                })
                ->get()
                ->sum(function ($l) {
                    return $l->duration ?: (Carbon::parse($l->from_date)->diffInDays(Carbon::parse($l->to_date)) + 1);
                });

            $remaining = max($allotted - $used, 0);
            if ($duration > $remaining) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient leave balance. You have {$remaining} day(s) remaining for {$leaveType->leave_name}, but requested {$duration} day(s).",
                    'remaining_days' => $remaining,
                    'requested_days' => $duration,
                ], 422);
            }
        }

        // Check for overlapping existing approved or pending leaves
        $overlapping = Leave::where('employee_id', $employee->employee_id)
            ->whereIn('status', ['DRAFT', 'SENT', 'APPROVED'])
            ->where(function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('from_date', [$fromDate->toDateString(), $toDate->toDateString()])
                    ->orWhereBetween('to_date', [$fromDate->toDateString(), $toDate->toDateString()])
                    ->orWhere(function ($sub) use ($fromDate, $toDate) {
                        $sub->where('from_date', '<=', $fromDate->toDateString())
                            ->where('to_date', '>=', $toDate->toDateString());
                    });
            })
            ->exists();

        if ($overlapping) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an active or pending leave request overlapping with these dates.',
            ], 422);
        }

        // Restricted Holiday (RH) conditional validation
        $isRestricted = str_contains(strtolower($leaveType->leave_name), 'restricted')
            || strcasecmp($leaveType->leave_code, 'RH-001') === 0;

        $holidayId = null;
        if ($isRestricted) {
            // Must be a single-day application
            if ($fromDate->toDateString() !== $toDate->toDateString()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Restricted Holiday leave can only be applied for a single day on the designated holiday date.',
                ], 422);
            }

            // Validate that the date is a declared holiday in holidays table
            $holiday = Holiday::whereDate('holiday_date', $fromDate->toDateString())
                ->where('status', 1)
                ->first();

            if (!$holiday) {
                return response()->json([
                    'success' => false,
                    'message' => "The selected date ({$fromDate->format('d M, Y')}) is not a declared Restricted Holiday.",
                ], 422);
            }

            $remarkLower = strtolower($holiday->holiday_remark ?? '');
            if (str_contains($remarkLower, 'common')) {
                return response()->json([
                    'success' => false,
                    'message' => "Office is officially closed on {$holiday->holiday_name} ({$fromDate->format('d M, Y')}) as a Common Mandatory Holiday. No leave application is required.",
                ], 422);
            }

            // Check if employee already applied for this specific holiday
            $alreadyAppliedForHoliday = Leave::where('employee_id', $employee->employee_id)
                ->where('holiday_id', $holiday->id)
                ->whereIn('status', ['DRAFT', 'SENT', 'APPROVED'])
                ->exists();

            if ($alreadyAppliedForHoliday) {
                return response()->json([
                    'success' => false,
                    'message' => "You have already submitted a leave request for {$holiday->holiday_name} ({$fromDate->format('d M, Y')}).",
                ], 422);
            }

            $holidayId = $holiday->id;
        }

        // Upload supporting document if present
        $documentPath = null;
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $documentPath = $file->store('leave_documents', 'public');
        }

        $leave = Leave::create([
            'employee_id' => $employee->employee_id,
            'leave_type_id' => $leaveType->id,
            'holiday_id' => $holidayId,
            'from_date' => $fromDate->toDateString(),
            'to_date' => $toDate->toDateString(),
            'duration' => $duration,
            'reason' => trim($request->input('reason')),
            'document_path' => $documentPath,
            'status' => 'SENT', // 'SENT' is pending supervisor action
        ]);

        // Create Activity Log
        LeaveActivityLog::create([
            'leave_id' => $leave->id,
            'actor_type' => 'Employee',
            'actor_id' => $employee->employee_id,
            'actor_name' => $employee->employee_name,
            'action' => 'applied',
            'remarks' => 'Leave application submitted by employee.',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => Carbon::now(),
        ]);

        // Email notification to Supervisor & Admin(s)
        \App\Services\LeaveNotificationService::notifyLeaveApplied($leave);

        return response()->json([
            'success' => true,
            'message' => 'Leave application submitted successfully.',
            'data' => $this->formatLeave($leave->fresh(['leaveType', 'activityLogs', 'holiday'])),
        ], 201);
    }

    /**
     * Get Employee's Leave Applications list.
     */
    public function list(Request $request)
    {
        $request->validate([
            'status' => 'nullable|string|in:ALL,DRAFT,SENT,APPROVED,REJECTED',
            'year' => 'nullable|integer|between:2020,2035',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $employee = $request->user();
        $query = Leave::with(['leaveType', 'approver', 'rejecter', 'holiday'])
            ->where('employee_id', $employee->employee_id);

        if ($request->filled('status') && strtoupper($request->status) !== 'ALL') {
            $query->where('status', strtoupper($request->status));
        }

        if ($request->filled('year')) {
            $year = $request->input('year');
            $query->where(function ($q) use ($year) {
                $q->whereYear('from_date', $year)->orWhereYear('to_date', $year);
            });
        }

        $perPage = (int) $request->input('per_page', 15);
        $leaves = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'records' => $leaves->map(fn ($l) => $this->formatLeave($l)),
                'pagination' => [
                    'current_page' => $leaves->currentPage(),
                    'last_page' => $leaves->lastPage(),
                    'per_page' => $leaves->perPage(),
                    'total_records' => $leaves->total(),
                ],
            ],
        ], 200);
    }

    /**
     * Get Leave Details by ID.
     */
    public function details(Request $request, $id)
    {
        $employee = $request->user();

        $leave = Leave::with(['leaveType', 'approver', 'rejecter', 'activityLogs', 'holiday'])
            ->where('employee_id', $employee->employee_id)
            ->where('id', $id)
            ->first();

        if (!$leave) {
            return response()->json([
                'success' => false,
                'message' => 'Leave application not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatLeave($leave),
        ], 200);
    }

    /**
     * Format a Leave record.
     */
    private function formatLeave(Leave $leave): array
    {
        $statusLabels = [
            'DRAFT' => 'Draft',
            'SENT' => 'Pending Approval',
            'APPROVED' => 'Approved',
            'REJECTED' => 'Rejected',
        ];

        return [
            'id' => $leave->id,
            'leave_type' => [
                'id' => $leave->leave_type_id,
                'name' => $leave->leaveType?->leave_name ?? $leave->leaveType?->name ?? 'Custom Leave',
                'leave_name' => $leave->leaveType?->leave_name ?? $leave->leaveType?->name ?? 'Custom Leave',
                'code' => $leave->leaveType?->leave_code,
                'category' => $leave->leaveType?->leave_category ?? 'Paid',
            ],
            'holiday' => $leave->holiday ? [
                'id' => $leave->holiday->id,
                'name' => $leave->holiday->holiday_name,
                'date' => $leave->holiday->holiday_date,
                'remark' => $leave->holiday->holiday_remark,
            ] : null,
            'from_date' => optional($leave->from_date)->toDateString(),
            'to_date' => optional($leave->to_date)->toDateString(),
            'duration_days' => $leave->duration,
            'reason' => $leave->reason,
            'document_url' => $leave->document_path ? asset('storage/' . $leave->document_path) : null,
            'status' => $leave->status,
            'status_label' => $statusLabels[$leave->status] ?? $leave->status,
            'supervisor_remarks' => $leave->supervisor_remarks,
            'action_at' => optional($leave->action_at)->toDateTimeString(),
            'action_by' => $leave->approver?->name ?? $leave->rejecter?->name ?? null,
            'created_at' => optional($leave->created_at)->toDateTimeString(),
            'timeline_activity' => $leave->activityLogs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'actor_name' => $log->actor_name,
                    'actor_type' => $log->actor_type,
                    'action' => $log->action,
                    'remarks' => $log->remarks,
                    'created_at' => optional($log->created_at)->toDateTimeString(),
                ];
            }),
        ];
    }
}
