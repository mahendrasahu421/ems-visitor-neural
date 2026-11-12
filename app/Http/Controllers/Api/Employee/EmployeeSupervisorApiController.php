<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Country;
use App\Models\CountryState;
use App\Models\StateCity;
use App\Models\DailyReport;
use App\Models\Designation;
use App\Models\EmailTemplate;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\LeaveActivityLog;
use App\Models\LeaveMapping;
use App\Models\LeaveType;
use App\Models\Project;
use App\Models\Shift;
use App\Models\User;
use App\Mail\LeaveStatusMail;
use App\Models\Company;
use App\Exports\SupervisorLeaveReportExport;
use App\Exports\SupervisorAttendanceLogExport;
use App\Exports\AttendanceRegisterExport;
use App\Http\Controllers\Attendance\AttendanceRegisterReportController;
use App\Services\ShiftAttendanceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeSupervisorApiController extends Controller
{
    /**
     * Helper: Ensure current employee has supervisor privileges.
     */
    protected function authorizeSupervisor(Request $request): ?Employee
    {
        $employee = $request->user();

        if (!$employee || !$employee->isSupervisor()) {
            return null;
        }

        return $employee;
    }

    /**
     * 1. Supervisor Dashboard / Overview
     * Provides high-level team metrics, today's attendance summary, and pending leaves.
     */
    public function dashboard(Request $request)
    {
        $supervisor = $this->authorizeSupervisor($request);
        if (!$supervisor) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Supervisor access required.',
            ], 403);
        }

        $teamQuery = $supervisor->teamQuery();
        $totalTeamMembers = (clone $teamQuery)->count();
        $teamMemberIds = (clone $teamQuery)->pluck('employee_id')->toArray();

        $today = Carbon::today()->toDateString();

        // Today's team attendances
        $todayAttendances = EmployeeAttendance::whereIn('employee_id', $teamMemberIds)
            ->whereDate('attendance_date', $today)
            ->get();

        $presentCount = $todayAttendances->count();

        // Late and early counts
        $shiftService = app(ShiftAttendanceService::class);
        $lateCount = 0;
        $earlyCount = 0;

        foreach ($todayAttendances as $att) {
            $emp = $att->employee;
            if ($att->check_in_at) {
                $eval = $shiftService->evaluate($att->check_in_at, $att->check_out_at, $emp?->shift);
                if (!empty($eval['is_late'])) {
                    $lateCount++;
                }
                if (!empty($eval['is_early_in'])) {
                    $earlyCount++;
                }
            }
        }

        // Today's approved leaves for team
        $onLeaveTodayCount = Leave::whereIn('employee_id', $teamMemberIds)
            ->where('status', 'APPROVED')
            ->where('from_date', '<=', $today)
            ->where('to_date', '>=', $today)
            ->count();

        $absentCount = max(0, $totalTeamMembers - $presentCount - $onLeaveTodayCount);

        // Pending team leaves
        $pendingLeavesCount = Leave::whereIn('employee_id', $teamMemberIds)
            ->whereIn('status', ['SENT', 'DRAFT', 'PENDING'])
            ->count();

        // Recent 5 leave requests from team
        $recentLeaves = Leave::with(['employee.designation', 'leaveType'])
            ->whereIn('employee_id', $teamMemberIds)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(fn ($l) => $this->formatTeamLeave($l));

        return response()->json([
            'success' => true,
            'data' => [
                'supervisor' => [
                    'id' => $supervisor->employee_id,
                    'name' => $supervisor->employee_name,
                    'designation' => $supervisor->designation?->name,
                ],
                'team_metrics' => [
                    'total_members' => $totalTeamMembers,
                    'present_today' => $presentCount,
                    'absent_today' => $absentCount,
                    'late_today' => $lateCount,
                    'early_in_today' => $earlyCount,
                    'on_leave_today' => $onLeaveTodayCount,
                    'pending_leaves_count' => $pendingLeavesCount,
                    'turnout_percentage' => $totalTeamMembers > 0 ? round(($presentCount / $totalTeamMembers) * 100, 1) : 0,
                ],
                'recent_leave_requests' => $recentLeaves,
            ],
        ], 200);
    }

    /**
     * 2. Team Leaves List
     * Lists leave applications submitted by subordinates with status filtering & pagination.
     */
    public function leaves(Request $request)
    {
        $supervisor = $this->authorizeSupervisor($request);
        if (!$supervisor) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Supervisor access required.',
            ], 403);
        }

        $request->validate([
            'status' => 'nullable|string|in:ALL,PENDING,SENT,APPROVED,REJECTED',
            'employee_id' => 'nullable|integer',
            'year' => 'nullable|integer|between:2020,2035',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $teamMemberIds = $supervisor->teamQuery()->pluck('employee_id')->toArray();

        $query = Leave::with(['employee.designation', 'leaveType', 'approver', 'rejecter', 'holiday'])
            ->whereIn('employee_id', $teamMemberIds);

        // Status filter
        if ($request->filled('status') && strtoupper($request->status) !== 'ALL') {
            $st = strtoupper($request->status);
            if ($st === 'PENDING') {
                $query->whereIn('status', ['SENT', 'DRAFT', 'PENDING']);
            } else {
                $query->where('status', $st);
            }
        }

        // Subordinate filter
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Year filter
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
                'records' => $leaves->map(fn ($l) => $this->formatTeamLeave($l)),
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
     * 3. Leave Details
     * Shows full leave information, applicant details, document attachment, and team conflict warning.
     */
    public function leaveDetails(Request $request, $id)
    {
        $supervisor = $this->authorizeSupervisor($request);
        if (!$supervisor) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Supervisor access required.',
            ], 403);
        }

        $teamMemberIds = $supervisor->teamQuery()->pluck('employee_id')->toArray();

        $leave = Leave::with(['employee.designation', 'employee.shift', 'leaveType', 'approver', 'rejecter', 'activityLogs', 'holiday'])
            ->whereIn('employee_id', $teamMemberIds)
            ->where('id', $id)
            ->first();

        if (!$leave) {
            return response()->json([
                'success' => false,
                'message' => 'Leave application not found or does not belong to your team.',
            ], 404);
        }

        // Team leave overlap check (check if other team members are already on approved leave during these dates)
        $conflictingLeaves = Leave::with('employee')
            ->whereIn('employee_id', $teamMemberIds)
            ->where('employee_id', '!=', $leave->employee_id)
            ->where('status', 'APPROVED')
            ->where(function ($q) use ($leave) {
                $from = $leave->from_date->toDateString();
                $to = $leave->to_date->toDateString();
                $q->whereBetween('from_date', [$from, $to])
                  ->orWhereBetween('to_date', [$from, $to])
                  ->orWhere(function ($sub) use ($from, $to) {
                      $sub->where('from_date', '<=', $from)->where('to_date', '>=', $to);
                  });
            })
            ->get();

        $data = $this->formatTeamLeave($leave);
        $data['team_conflicts'] = [
            'count' => $conflictingLeaves->count(),
            'other_members_on_leave' => $conflictingLeaves->map(fn ($c) => [
                'employee_name' => $c->employee?->employee_name,
                'from_date' => optional($c->from_date)->toDateString(),
                'to_date' => optional($c->to_date)->toDateString(),
            ]),
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200);
    }

    /**
     * 4. Approve Subordinate's Leave
     */
    public function approveLeave(Request $request, $id)
    {
        $supervisor = $this->authorizeSupervisor($request);
        if (!$supervisor) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Supervisor access required.',
            ], 403);
        }

        $request->validate([
            'remarks' => 'nullable|string|max:1000',
        ]);

        $teamMemberIds = $supervisor->teamQuery()->pluck('employee_id')->toArray();

        $leave = Leave::with('employee')
            ->whereIn('employee_id', $teamMemberIds)
            ->where('id', $id)
            ->first();

        if (!$leave) {
            return response()->json([
                'success' => false,
                'message' => 'Leave application not found or does not belong to your team.',
            ], 404);
        }

        if (strtoupper($leave->status) === 'APPROVED') {
            return response()->json([
                'success' => false,
                'message' => 'This leave application is already approved.',
            ], 400);
        }

        // Resolve web user ID if linked, otherwise default to null or user matching email
        $userObj = User::where('email', $supervisor->employee_email)->first();
        $userId = $userObj?->id ?? null;

        $remarks = trim($request->input('remarks', 'Approved by supervisor via mobile app.'));

        $leave->status = 'APPROVED';
        $leave->approved_by = $userId;
        $leave->supervisor_remarks = $remarks;
        $leave->action_at = Carbon::now();
        $leave->save();

        // Audit Trail
        LeaveActivityLog::create([
            'leave_id' => $leave->id,
            'actor_type' => 'Supervisor',
            'actor_id' => $supervisor->employee_id,
            'actor_name' => $supervisor->employee_name,
            'action' => 'approved',
            'remarks' => $remarks,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => Carbon::now(),
        ]);

        // Attempt Email Dispatch
        $this->dispatchLeaveEmail($leave, 'Approved', $supervisor->employee_name, $remarks);

        return response()->json([
            'success' => true,
            'message' => "Leave application for {$leave->employee?->employee_name} has been approved successfully.",
            'data' => $this->formatTeamLeave($leave->fresh(['employee.designation', 'leaveType', 'activityLogs'])),
        ], 200);
    }

    /**
     * 5. Reject Subordinate's Leave
     */
    public function rejectLeave(Request $request, $id)
    {
        $supervisor = $this->authorizeSupervisor($request);
        if (!$supervisor) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Supervisor access required.',
            ], 403);
        }

        $request->validate([
            'reason' => 'required_without:remarks|nullable|string|min:3|max:1000',
            'remarks' => 'required_without:reason|nullable|string|min:3|max:1000',
        ]);

        $teamMemberIds = $supervisor->teamQuery()->pluck('employee_id')->toArray();

        $leave = Leave::with('employee')
            ->whereIn('employee_id', $teamMemberIds)
            ->where('id', $id)
            ->first();

        if (!$leave) {
            return response()->json([
                'success' => false,
                'message' => 'Leave application not found or does not belong to your team.',
            ], 404);
        }

        if (strtoupper($leave->status) === 'REJECTED') {
            return response()->json([
                'success' => false,
                'message' => 'This leave application is already rejected.',
            ], 400);
        }

        $userObj = User::where('email', $supervisor->employee_email)->first();
        $userId = $userObj?->id ?? null;

        $rejectionReason = trim($request->input('reason') ?: $request->input('remarks'));

        $leave->status = 'REJECTED';
        $leave->rejected_by = $userId;
        $leave->supervisor_remarks = $rejectionReason;
        $leave->action_at = Carbon::now();
        $leave->save();

        // Audit Trail
        LeaveActivityLog::create([
            'leave_id' => $leave->id,
            'actor_type' => 'Supervisor',
            'actor_id' => $supervisor->employee_id,
            'actor_name' => $supervisor->employee_name,
            'action' => 'rejected',
            'remarks' => $rejectionReason,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => Carbon::now(),
        ]);

        // Attempt Email Dispatch
        $this->dispatchLeaveEmail($leave, 'Rejected', $supervisor->employee_name, $rejectionReason);

        return response()->json([
            'success' => true,
            'message' => "Leave application for {$leave->employee?->employee_name} has been rejected.",
            'data' => $this->formatTeamLeave($leave->fresh(['employee.designation', 'leaveType', 'activityLogs'])),
        ], 200);
    }

    /**
     * 6. Team Attendance Status Roster
     * View today's or any date's attendance punches of all supervised team members.
     */
    public function teamAttendance(Request $request)
    {
        $supervisor = $this->authorizeSupervisor($request);
        if (!$supervisor) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Supervisor access required.',
            ], 403);
        }

        $request->validate([
            'date' => 'nullable|date_format:Y-m-d',
            'status' => 'nullable|string|in:ALL,PRESENT,ABSENT,LATE,EARLY_IN,ON_LEAVE',
        ]);

        $targetDate = $request->input('date', Carbon::today()->toDateString());
        $teamMembers = $supervisor->teamQuery()->with(['designation', 'shift'])->get();
        $teamMemberIds = $teamMembers->pluck('employee_id')->toArray();

        $attendances = EmployeeAttendance::whereIn('employee_id', $teamMemberIds)
            ->whereDate('attendance_date', $targetDate)
            ->get()
            ->keyBy('employee_id');

        $approvedLeaves = Leave::whereIn('employee_id', $teamMemberIds)
            ->where('status', 'APPROVED')
            ->where('from_date', '<=', $targetDate)
            ->where('to_date', '>=', $targetDate)
            ->get()
            ->keyBy('employee_id');

        $shiftService = app(ShiftAttendanceService::class);
        $roster = [];

        foreach ($teamMembers as $member) {
            $att = $attendances->get($member->employee_id);
            $leave = $approvedLeaves->get($member->employee_id);

            $eval = null;
            $status = 'ABSENT';
            $statusLabel = 'Absent';

            if ($att && $att->check_in_at) {
                $eval = $shiftService->evaluate($att->check_in_at, $att->check_out_at, $member->shift);
                if (!empty($eval['is_late'])) {
                    $status = 'LATE';
                    $statusLabel = 'Late In (' . ($eval['late_formatted'] ?? '') . ')';
                } elseif (!empty($eval['is_early_in'])) {
                    $status = 'EARLY_IN';
                    $statusLabel = 'Early In (' . ($eval['early_in_formatted'] ?? '') . ')';
                } else {
                    $status = 'PRESENT';
                    $statusLabel = 'On Time';
                }
            } elseif ($leave) {
                $status = 'ON_LEAVE';
                $statusLabel = 'On Leave (' . ($leave->leaveType?->name ?? 'Approved') . ')';
            }

            // Filter check
            if ($request->filled('status') && strtoupper($request->status) !== 'ALL') {
                $st = strtoupper($request->status);
                if ($st === 'PRESENT' && !in_array($status, ['PRESENT', 'LATE', 'EARLY_IN'])) {
                    continue;
                } elseif ($st !== 'PRESENT' && $status !== $st) {
                    continue;
                }
            }

            $roster[] = [
                'employee_id' => $member->employee_id,
                'employee_name' => $member->employee_name,
                'designation' => $member->designation?->name ?? 'Staff Member',
                'profile_image' => $member->profile_image ? asset('storage/' . $member->profile_image) : null,
                'mobile' => $member->employee_mobile,
                'shift' => [
                    'name' => $member->shift?->shift_name ?? 'General Shift',
                    'start_time' => $member->shift?->start_time ?? '09:30:00',
                    'end_time' => $member->shift?->end_time ?? '18:30:00',
                    'grace_period' => $member->shift?->grace_period ?? 15,
                ],
                'status' => $status,
                'status_label' => $statusLabel,
                'check_in' => $att?->check_in_at ? [
                    'time' => Carbon::parse($att->check_in_at)->format('h:i A'),
                    'datetime' => Carbon::parse($att->check_in_at)->toIso8601String(),
                    'is_late' => (bool) ($eval['is_late'] ?? false),
                    'late_minutes' => (int) ($eval['late_minutes'] ?? 0),
                    'is_early_in' => (bool) ($eval['is_early_in'] ?? false),
                    'early_in_minutes' => (int) ($eval['early_in_minutes'] ?? 0),
                    'note' => $att->check_in_note,
                ] : null,
                'check_out' => $att?->check_out_at ? [
                    'time' => Carbon::parse($att->check_out_at)->format('h:i A'),
                    'datetime' => Carbon::parse($att->check_out_at)->toIso8601String(),
                    'is_early_out' => (bool) ($eval['is_early_out'] ?? false),
                    'early_out_minutes' => (int) ($eval['early_out_minutes'] ?? 0),
                    'note' => $att->check_out_note,
                ] : null,
                'work_duration' => $att ? $att->worked_formatted : '0h 0m 0s',
                'work_clock' => $att ? $att->worked_clock : '00:00:00',
                'timer' => $att ? $att->getTimerPayload() : EmployeeAttendance::defaultTimerPayload(),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $targetDate,
                'total_team_members' => count($teamMembers),
                'filtered_count' => count($roster),
                'roster' => $roster,
            ],
        ], 200);
    }

    /**
     * 7. Team Members Directory
     * List all subordinates reporting to this supervisor.
     */
    public function teamMembers(Request $request)
    {
        $supervisor = $this->authorizeSupervisor($request);
        if (!$supervisor) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Supervisor access required.',
            ], 403);
        }

        $query = $supervisor->teamQuery()->with(['designation', 'agency', 'shift', 'city', 'state']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('employee_name', 'like', "%{$search}%")
                  ->orWhere('employee_email', 'like', "%{$search}%")
                  ->orWhere('employee_mobile', 'like', "%{$search}%");
            });
        }

        $members = $query->orderBy('employee_name')->get();
        $today = Carbon::today()->toDateString();

        $todayAttendances = EmployeeAttendance::whereIn('employee_id', $members->pluck('employee_id'))
            ->whereDate('attendance_date', $today)
            ->get()
            ->keyBy('employee_id');

        return response()->json([
            'success' => true,
            'data' => [
                'total_members' => $members->count(),
                'members' => $members->map(function ($m) use ($todayAttendances) {
                    $att = $todayAttendances->get($m->employee_id);
                    return [
                        'id' => $m->employee_id,
                        'name' => $m->employee_name,
                        'email' => $m->employee_email,
                        'mobile' => $m->employee_mobile,
                        'gender' => $m->employee_gender,
                        'designation' => $m->designation?->name ?? 'Staff Member',
                        'profile_image' => $m->profile_image ? asset('storage/' . $m->profile_image) : null,
                        'agency_name' => $m->agency?->agency_name ?? 'Direct / In-House',
                        'shift' => [
                            'name' => $m->shift?->shift_name ?? 'General Shift',
                            'timing' => ($m->shift?->start_time ?? '09:30') . ' - ' . ($m->shift?->end_time ?? '18:30'),
                        ],
                        'location' => trim(($m->city?->name ?? '') . ', ' . ($m->state?->name ?? ''), ', '),
                        'employment_type' => $m->employment_type,
                        'status' => $m->status,
                        'today_punch' => $att ? [
                            'punched_in' => true,
                            'check_in_time' => Carbon::parse($att->check_in_at)->format('h:i A'),
                            'punched_out' => !empty($att->check_out_at),
                            'check_out_time' => $att->check_out_at ? Carbon::parse($att->check_out_at)->format('h:i A') : null,
                            'work_clock' => $att->worked_clock,
                            'work_duration' => $att->worked_formatted,
                            'timer' => $att->getTimerPayload(),
                        ] : [
                            'punched_in' => false,
                            'punched_out' => false,
                            'check_in_time' => null,
                            'check_out_time' => null,
                            'work_clock' => '00:00:00',
                            'work_duration' => '0h 0m 0s',
                            'timer' => EmployeeAttendance::defaultTimerPayload(),
                        ],
                    ];
                }),
            ],
        ], 200);
    }

    /**
     * Form Options / Metadata for adding and updating subordinates.
     */
    public function formOptions(Request $request)
    {
        $supervisor = $this->authorizeSupervisor($request);
        if (!$supervisor) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Supervisor access required.',
            ], 403);
        }

        $designations = Designation::where('status', 'Active')
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        $agencies = Agency::where('status', 'Active')
            ->orderBy('agency_name')
            ->get(['id', 'agency_name', 'agency_code']);

        $shifts = Shift::where('status', 'Active')
            ->orderBy('shift_name')
            ->get(['id', 'shift_name', 'start_time', 'end_time', 'grace_period']);

        $countries = Country::orderBy('name')->get(['id', 'name']);

        $states = [];
        if ($request->filled('country_id')) {
            $states = CountryState::where('country_id', (int) $request->input('country_id'))
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        $cities = [];
        if ($request->filled('state_id')) {
            $cities = StateCity::where('state_id', (int) $request->input('state_id'))
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'designations' => $designations,
                'agencies' => $agencies,
                'shifts' => $shifts,
                'countries' => $countries,
                'states' => $states,
                'cities' => $cities,
                'blood_groups' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', '-'],
                'employment_types' => ['Full-time', 'Part-time', 'Contract', 'Intern', 'Probation'],
                'genders' => [
                    ['id' => 1, 'name' => 'Male'],
                    ['id' => 2, 'name' => 'Female'],
                    ['id' => 3, 'name' => 'Other'],
                ],
            ],
        ], 200);
    }

    /**
     * Add a new employee under this supervisor.
     */
    public function createMember(Request $request)
    {
        $supervisor = $this->authorizeSupervisor($request);
        if (!$supervisor) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Supervisor access required.',
            ], 403);
        }

        $rules = [
            'name' => 'required_without:employee_name|string|max:150',
            'employee_name' => 'required_without:name|string|max:150',
            'email' => 'required_without:employee_email|email|max:150|unique:employees,employee_email',
            'employee_email' => 'required_without:email|email|max:150|unique:employees,employee_email',
            'mobile' => 'required_without:employee_mobile|string|max:20|unique:employees,employee_mobile',
            'employee_mobile' => 'required_without:mobile|string|max:20|unique:employees,employee_mobile',
            'password' => 'required_without:employee_password|string|min:6|max:100',
            'employee_password' => 'required_without:password|string|min:6|max:100',
            'gender' => 'nullable|in:1,2,3,male,female,other,Male,Female,Other',
            'employee_gender' => 'nullable|in:1,2,3,male,female,other,Male,Female,Other',
            'designation_id' => 'required|exists:designations,id',
            'agency_id' => 'nullable|exists:agencies,id',
            'shift_id' => 'nullable|exists:shifts,id',
            'employment_type' => 'nullable|string|max:50',
            'blood_group' => 'nullable|string|max:10',
            'joining_date' => 'nullable|date_format:Y-m-d',
            'date_of_confirmation' => 'nullable|date_format:Y-m-d|after_or_equal:joining_date',
            'posting_country' => 'nullable',
            'posting_state' => 'nullable',
            'posting_city' => 'nullable',
            'emergency_contact' => 'nullable|string|max:50',
            'current_address' => 'nullable|string|max:500',
            'profile_image' => 'nullable',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $empName = trim($request->input('employee_name') ?: $request->input('name'));
        $empEmail = strtolower(trim($request->input('employee_email') ?: $request->input('email')));
        $empMobile = trim($request->input('employee_mobile') ?: $request->input('mobile'));
        $rawPassword = (string) ($request->input('employee_password') ?: $request->input('password'));

        // Normalize gender
        $rawGender = $request->input('employee_gender') ?: $request->input('gender', 1);
        $gender = 1;
        if (in_array(strtolower((string) $rawGender), ['2', 'female', 'f'])) {
            $gender = 2;
        } elseif (in_array(strtolower((string) $rawGender), ['3', 'other', 'o'])) {
            $gender = 3;
        }

        // Image upload handling
        $imageName = '1770807033_users-default.png';
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $imageName = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/employees'), $imageName);
        } elseif ($request->filled('profile_image') && is_string($request->input('profile_image'))) {
            $imageName = $request->input('profile_image');
        }

        // Default shift fallback
        $defaultShift = ShiftAttendanceService::getDefaultShift();
        $shiftId = $request->filled('shift_id') ? (int) $request->input('shift_id') : ($defaultShift?->id ?? 1);

        // Agency fallback to supervisor's agency if not provided
        $agencyId = $request->filled('agency_id') ? (int) $request->input('agency_id') : ($supervisor->agency_id ?? null);

        // Check if designation implies supervisor role
        $isSupervisor = false;
        $designation = Designation::find($request->input('designation_id'));
        if ($designation && stripos($designation->name, 'Supervisor') !== false) {
            $isSupervisor = true;
        }

        $countryId = $request->input('posting_country') ?: $request->input('country_id');
        if (empty($countryId)) {
            $countryId = $supervisor->posting_country ?: (Country::value('id') ?? 101);
        }

        $stateId = $request->input('posting_state') ?: $request->input('state_id');
        if (empty($stateId)) {
            $stateId = $supervisor->posting_state ?: (CountryState::where('country_id', $countryId)->value('id') ?? CountryState::value('id') ?? 1);
        }

        $cityId = $request->input('posting_city') ?: $request->input('city_id');
        if (empty($cityId)) {
            $cityId = $supervisor->posting_city ?: (StateCity::where('state_id', $stateId)->value('id') ?? StateCity::value('id') ?? 1);
        }

        $newEmployee = Employee::create([
            'employee_name' => $empName,
            'employee_email' => $empEmail,
            'employee_mobile' => $empMobile,
            'employee_password' => Hash::make($rawPassword),
            'employee_gender' => $gender,
            'blood_group' => $request->input('blood_group', '-'),
            'posting_country' => $countryId,
            'posting_state' => $stateId,
            'posting_city' => $cityId,
            'joining_date' => $request->input('joining_date', Carbon::today()->toDateString()),
            'date_of_confirmation' => $request->input('date_of_confirmation'),
            'employment_type' => $request->input('employment_type', 'Full-time'),
            'designation_id' => (int) $request->input('designation_id'),
            'agency_id' => $agencyId,
            'shift_id' => $shiftId,
            'is_supervisor' => $isSupervisor ? 1 : 0,
            'supervisor_id' => $supervisor->employee_id, // Directly mapped under this supervisor!
            'status' => 'Active',
            'profile_image' => $imageName,
        ]);

        $newEmployee->load(['designation', 'agency', 'shift', 'country', 'state', 'city']);

        $profileImageUrl = null;
        if (!empty($newEmployee->profile_image)) {
            if (file_exists(public_path('uploads/employees/' . $newEmployee->profile_image))) {
                $profileImageUrl = asset('uploads/employees/' . $newEmployee->profile_image);
            } else {
                $profileImageUrl = asset('storage/' . $newEmployee->profile_image);
            }
        }

        try {
            app(\App\Services\FirebaseNotificationService::class)->sendToEmployee(
                $supervisor->employee_id,
                'Team Member Added',
                "New employee {$newEmployee->employee_name} ({$newEmployee->employee_email}) was added to your team.",
                ['type' => 'team_member_added', 'employee_id' => $newEmployee->employee_id],
                ['sender_type' => 'supervisor', 'sender_id' => $supervisor->employee_id]
            );
        } catch (\Throwable $e) {
            // Non-blocking
        }

        return response()->json([
            'success' => true,
            'message' => "Employee '{$newEmployee->employee_name}' created successfully and added to your team.",
            'data' => [
                'employee_id' => $newEmployee->employee_id,
                'employee_name' => $newEmployee->employee_name,
                'employee_email' => $newEmployee->employee_email,
                'employee_mobile' => $newEmployee->employee_mobile,
                'employee_gender' => $newEmployee->employee_gender == 1 ? 'Male' : ($newEmployee->employee_gender == 2 ? 'Female' : 'Other'),
                'blood_group' => $newEmployee->blood_group,
                'designation' => [
                    'id' => $newEmployee->designation_id,
                    'name' => $newEmployee->designation?->name ?? 'Staff Member',
                ],
                'agency' => [
                    'id' => $newEmployee->agency_id,
                    'name' => $newEmployee->agency?->agency_name ?? 'Direct / In-House',
                ],
                'shift' => [
                    'id' => $newEmployee->shift_id,
                    'name' => $newEmployee->shift?->shift_name ?? 'General Shift',
                    'timing' => ($newEmployee->shift?->start_time ?? '09:30') . ' - ' . ($newEmployee->shift?->end_time ?? '18:30'),
                ],
                'supervisor' => [
                    'id' => $supervisor->employee_id,
                    'name' => $supervisor->employee_name,
                ],
                'status' => $newEmployee->status,
                'employment_type' => $newEmployee->employment_type,
                'joining_date' => optional($newEmployee->joining_date)->toDateString() ?: $newEmployee->joining_date,
                'profile_image' => $profileImageUrl,
            ],
        ], 201);
    }

    /**
     * Update a subordinate's details.
     */
    public function updateMember(Request $request, $employee_id)
    {
        $supervisor = $this->authorizeSupervisor($request);
        if (!$supervisor) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Supervisor access required.',
            ], 403);
        }

        $teamMemberIds = $supervisor->teamQuery()->pluck('employee_id')->toArray();
        if (!in_array($employee_id, $teamMemberIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found or does not belong to your team.',
            ], 404);
        }

        $employee = Employee::findOrFail($employee_id);

        $rules = [
            'name' => 'nullable|string|max:150',
            'employee_name' => 'nullable|string|max:150',
            'mobile' => 'nullable|string|max:20|unique:employees,employee_mobile,' . $employee->employee_id . ',employee_id',
            'employee_mobile' => 'nullable|string|max:20|unique:employees,employee_mobile,' . $employee->employee_id . ',employee_id',
            'gender' => 'nullable|in:1,2,3,male,female,other,Male,Female,Other',
            'designation_id' => 'nullable|exists:designations,id',
            'agency_id' => 'nullable|exists:agencies,id',
            'shift_id' => 'nullable|exists:shifts,id',
            'employment_type' => 'nullable|string|max:50',
            'blood_group' => 'nullable|string|max:10',
            'date_of_confirmation' => 'nullable|date_format:Y-m-d',
            'status' => 'nullable|in:Active,Inactive,active,inactive',
            'posting_country' => 'nullable',
            'posting_state' => 'nullable',
            'posting_city' => 'nullable',
            'password' => 'nullable|string|min:6|max:100',
            'profile_image' => 'nullable',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($request->filled('employee_name') || $request->filled('name')) {
            $employee->employee_name = trim($request->input('employee_name') ?: $request->input('name'));
        }

        if ($request->filled('employee_mobile') || $request->filled('mobile')) {
            $employee->employee_mobile = trim($request->input('employee_mobile') ?: $request->input('mobile'));
        }

        if ($request->filled('employee_gender') || $request->filled('gender')) {
            $rawGender = $request->input('employee_gender') ?: $request->input('gender');
            if (in_array(strtolower((string) $rawGender), ['2', 'female', 'f'])) {
                $employee->employee_gender = 2;
            } elseif (in_array(strtolower((string) $rawGender), ['3', 'other', 'o'])) {
                $employee->employee_gender = 3;
            } else {
                $employee->employee_gender = 1;
            }
        }

        if ($request->filled('designation_id')) {
            $employee->designation_id = (int) $request->input('designation_id');
        }

        if ($request->filled('agency_id')) {
            $employee->agency_id = (int) $request->input('agency_id');
        }

        if ($request->filled('shift_id')) {
            $employee->shift_id = (int) $request->input('shift_id');
        }

        if ($request->filled('employment_type')) {
            $employee->employment_type = $request->input('employment_type');
        }

        if ($request->filled('blood_group')) {
            $employee->blood_group = $request->input('blood_group');
        }

        if ($request->filled('date_of_confirmation')) {
            $employee->date_of_confirmation = $request->input('date_of_confirmation');
        }

        if ($request->filled('status')) {
            $employee->status = ucfirst(strtolower($request->input('status')));
        }

        if ($request->filled('posting_country') || $request->filled('country_id')) {
            $employee->posting_country = $request->input('posting_country') ?: $request->input('country_id');
        }
        if ($request->filled('posting_state') || $request->filled('state_id')) {
            $employee->posting_state = $request->input('posting_state') ?: $request->input('state_id');
        }
        if ($request->filled('posting_city') || $request->filled('city_id')) {
            $employee->posting_city = $request->input('posting_city') ?: $request->input('city_id');
        }

        if ($request->filled('password')) {
            $employee->employee_password = Hash::make($request->input('password'));
        }

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $imageName = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/employees'), $imageName);
            $employee->profile_image = $imageName;
        }

        $employee->save();
        if (!$employee->isActive()) {
            $employee->deactivate();
        }
        $employee->load(['designation', 'agency', 'shift']);

        return response()->json([
            'success' => true,
            'message' => "Subordinate '{$employee->employee_name}' updated successfully.",
            'data' => [
                'employee_id' => $employee->employee_id,
                'employee_name' => $employee->employee_name,
                'employee_email' => $employee->employee_email,
                'employee_mobile' => $employee->employee_mobile,
                'designation' => $employee->designation?->name,
                'agency' => $employee->agency?->agency_name,
                'shift' => $employee->shift?->shift_name,
                'status' => $employee->status,
                'employment_type' => $employee->employment_type,
            ],
        ], 200);
    }

    /**
     * 8. Subordinate Monthly Attendance History
     */
    public function memberAttendanceHistory(Request $request, $employee_id)
    {
        $supervisor = $this->authorizeSupervisor($request);
        if (!$supervisor) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Supervisor access required.',
            ], 403);
        }

        $teamMemberIds = $supervisor->teamQuery()->pluck('employee_id')->toArray();

        if (!in_array($employee_id, $teamMemberIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found or does not belong to your team.',
            ], 404);
        }

        $employee = Employee::with(['designation', 'shift'])->findOrFail($employee_id);

        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = (clone $startDate)->endOfMonth();

        $attendances = EmployeeAttendance::where('employee_id', $employee_id)
            ->whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->keyBy(fn ($a) => Carbon::parse($a->attendance_date)->toDateString());

        $leaves = Leave::where('employee_id', $employee_id)
            ->where('status', 'APPROVED')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('from_date', [$startDate->toDateString(), $endDate->toDateString()])
                  ->orWhereBetween('to_date', [$startDate->toDateString(), $endDate->toDateString()]);
            })
            ->get();

        $shiftService = app(ShiftAttendanceService::class);
        $daysInMonth = $endDate->day;
        $dailyRecords = [];

        $presentDays = 0;
        $lateDays = 0;
        $absentDays = 0;
        $leaveDays = 0;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $current = Carbon::createFromDate($year, $month, $day);
            $dateStr = $current->toDateString();
            $isSunday = $current->isSunday();
            $isFuture = $current->isFuture();

            $att = $attendances->get($dateStr);
            $hasLeave = $leaves->first(function ($l) use ($dateStr) {
                return $dateStr >= $l->from_date->toDateString() && $dateStr <= $l->to_date->toDateString();
            });

            $status = 'ABSENT';
            $statusLabel = 'Absent';
            $eval = null;

            if ($att && $att->check_in_at) {
                $presentDays++;
                $eval = $shiftService->evaluate($att->check_in_at, $att->check_out_at, $employee->shift);
                if (!empty($eval['is_late'])) {
                    $lateDays++;
                    $status = 'LATE';
                    $statusLabel = 'Late In';
                } else {
                    $status = 'PRESENT';
                    $statusLabel = 'Present';
                }
            } elseif ($hasLeave) {
                $leaveDays++;
                $status = 'ON_LEAVE';
                $statusLabel = 'Approved Leave';
            } elseif ($isSunday) {
                $status = 'WEEKLY_OFF';
                $statusLabel = 'Sunday Off';
            } elseif (!$isFuture) {
                $absentDays++;
            } else {
                $status = 'UPCOMING';
                $statusLabel = 'Upcoming';
            }

            $dailyRecords[] = [
                'date' => $dateStr,
                'day' => $current->format('l'),
                'status' => $status,
                'status_label' => $statusLabel,
                'check_in_time' => $att?->check_in_at ? Carbon::parse($att->check_in_at)->format('h:i A') : null,
                'check_out_time' => $att?->check_out_at ? Carbon::parse($att->check_out_at)->format('h:i A') : null,
                'is_late' => (bool) ($eval['is_late'] ?? false),
                'late_minutes' => (int) ($eval['late_minutes'] ?? 0),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'employee' => [
                    'id' => $employee->employee_id,
                    'name' => $employee->employee_name,
                    'designation' => $employee->designation?->name,
                ],
                'month' => $month,
                'year' => $year,
                'summary' => [
                    'total_days' => $daysInMonth,
                    'present_days' => $presentDays,
                    'late_days' => $lateDays,
                    'absent_days' => $absentDays,
                    'approved_leave_days' => $leaveDays,
                ],
                'daily_records' => $dailyRecords,
            ],
        ], 200);
    }

    /**
     * 9. Subordinate Full Profile (Admin-level detail on mobile app)
     */
    public function getMemberProfile(Request $request, $employee_id)
    {
        $supervisor = $this->authorizeSupervisor($request);
        if (!$supervisor) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Supervisor access required.',
            ], 403);
        }

        $teamMemberIds = $supervisor->teamQuery()->pluck('employee_id')->toArray();
        if (!in_array($employee_id, $teamMemberIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found or does not belong to your team.',
            ], 404);
        }

        $member = Employee::with([
            'designation',
            'agency',
            'shift',
            'city',
            'state',
            'country',
            'supervisorEmployee',
        ])->findOrFail($employee_id);

        $now = Carbon::now();
        $startOfMonth = (clone $now)->startOfMonth();
        $endOfMonth = (clone $now)->endOfMonth();

        // Attendances this month
        $attendances = EmployeeAttendance::where('employee_id', $employee_id)
            ->whereBetween('attendance_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->get();

        $presentDays = $attendances->whereNotNull('check_in_at')->count();
        $shiftService = app(ShiftAttendanceService::class);
        $lateDays = 0;
        foreach ($attendances as $att) {
            if ($att->check_in_at) {
                $eval = $shiftService->evaluate($att->check_in_at, $att->check_out_at, $member->shift);
                if (!empty($eval['is_late'])) {
                    $lateDays++;
                }
            }
        }

        $approvedLeavesThisMonth = Leave::where('employee_id', $employee_id)
            ->where('status', 'APPROVED')
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('from_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
                  ->orWhereBetween('to_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()]);
            })
            ->count();

        // Leave Balances
        $leaveBalances = $this->calculateEmployeeLeaveBalances($member, $now->year);

        // Recent 5 attendances
        $recentAttendances = EmployeeAttendance::where('employee_id', $employee_id)
            ->orderBy('attendance_date', 'desc')
            ->take(5)
            ->get()
            ->map(function ($att) use ($shiftService, $member) {
                $eval = $att->check_in_at ? $shiftService->evaluate($att->check_in_at, $att->check_out_at, $member->shift) : null;
                return [
                    'date' => optional($att->attendance_date)->toDateString(),
                    'check_in' => $att->check_in_at ? Carbon::parse($att->check_in_at)->format('h:i A') : null,
                    'check_out' => $att->check_out_at ? Carbon::parse($att->check_out_at)->format('h:i A') : null,
                    'is_late' => (bool) ($eval['is_late'] ?? false),
                    'is_regularized' => (bool) $att->is_regularized,
                    'status' => $att->status,
                ];
            });

        // Recent 5 leaves
        $recentLeaves = Leave::with(['leaveType', 'holiday'])
            ->where('employee_id', $employee_id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(fn ($l) => $this->formatTeamLeave($l));

        $avatarUrl = null;
        if (!empty($member->profile_image)) {
            if (file_exists(public_path('uploads/employees/' . $member->profile_image))) {
                $avatarUrl = asset('uploads/employees/' . $member->profile_image);
            } elseif (file_exists(storage_path('app/public/' . $member->profile_image))) {
                $avatarUrl = asset('storage/' . $member->profile_image);
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'employee' => [
                    'id' => $member->employee_id,
                    'name' => $member->employee_name,
                    'email' => $member->employee_email,
                    'mobile' => $member->employee_mobile,
                    'gender' => $member->employee_gender,
                    'blood_group' => $member->blood_group ?: '-',
                    'profile_image' => $avatarUrl,
                    'dob' => $member->dob ? Carbon::parse($member->dob)->format('d M, Y') : null,
                    'joining_date' => $member->joining_date ? Carbon::parse($member->joining_date)->format('d M, Y') : null,
                    'emergency_contact' => $member->emergency_contact,
                    'address' => trim(($member->current_address ?? '') . ' ' . ($member->city?->name ?? '') . ' ' . ($member->state?->name ?? ''), ' '),
                    'status' => $member->status,
                    'employment_type' => $member->employment_type,
                ],
                'agency' => [
                    'id' => $member->agency_id,
                    'name' => $member->agency?->agency_name ?? 'Direct / In-House',
                    'code' => $member->agency?->agency_code,
                    'contact_person' => $member->agency?->contact_person,
                    'phone' => $member->agency?->phone,
                    'email' => $member->agency?->email,
                ],
                'designation' => [
                    'id' => $member->designation_id,
                    'name' => $member->designation?->name ?? 'Staff Member',
                    'code' => $member->designation?->code,
                ],
                'shift' => [
                    'id' => $member->shift_id,
                    'name' => $member->shift?->shift_name ?? 'General Shift',
                    'timing' => ($member->shift?->start_time ?? '09:30') . ' - ' . ($member->shift?->end_time ?? '18:30'),
                    'grace_period' => $member->shift?->grace_period ?? 15,
                ],
                'supervisor' => [
                    'id' => $supervisor->employee_id,
                    'name' => $supervisor->employee_name,
                ],
                'current_month_summary' => [
                    'month_name' => $now->format('F Y'),
                    'present_days' => $presentDays,
                    'late_days' => $lateDays,
                    'approved_leaves' => $approvedLeavesThisMonth,
                ],
                'leave_balances' => $leaveBalances,
                'recent_attendances' => $recentAttendances,
                'recent_leaves' => $recentLeaves,
            ],
        ], 200);
    }

    /**
     * 10. Subordinate Leave Balances View
     */
    public function memberLeaveBalances(Request $request, $employee_id)
    {
        $supervisor = $this->authorizeSupervisor($request);
        if (!$supervisor) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Supervisor access required.',
            ], 403);
        }

        $teamMemberIds = $supervisor->teamQuery()->pluck('employee_id')->toArray();
        if (!in_array($employee_id, $teamMemberIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found or does not belong to your team.',
            ], 404);
        }

        $member = Employee::with(['designation'])->findOrFail($employee_id);
        $year = (int) $request->input('year', Carbon::now()->year);

        $balances = $this->calculateEmployeeLeaveBalances($member, $year);

        return response()->json([
            'success' => true,
            'data' => [
                'employee' => [
                    'id' => $member->employee_id,
                    'name' => $member->employee_name,
                    'designation' => $member->designation?->name,
                ],
                'year' => $year,
                'balances' => $balances,
            ],
        ], 200);
    }

    /**
     * 11. Regularize Attendance for a Subordinate (Manual Punch / Fix)
     */
    public function regularizeAttendance(Request $request, $employee_id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Attendance regularization feature has been disabled by company policy. Only live mobile punches with geolocation are permitted.',
        ], 403);

        $teamMemberIds = $supervisor->teamQuery()->pluck('employee_id')->toArray();
        if (!in_array($employee_id, $teamMemberIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found or does not belong to your team.',
            ], 404);
        }

        $request->validate([
            'attendance_date' => 'required|date_format:Y-m-d|before_or_equal:today',
            'check_in_time' => 'nullable|string',
            'check_out_time' => 'nullable|string',
            'reason' => 'required|string|min:3|max:1000',
        ]);

        if (!$request->filled('check_in_time') && !$request->filled('check_out_time')) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide at least check-in time or check-out time.',
            ], 422);
        }

        $member = Employee::with('shift')->findOrFail($employee_id);
        $dateStr = $request->input('attendance_date');

        $attendance = EmployeeAttendance::firstOrNew([
            'employee_id' => $employee_id,
            'attendance_date' => $dateStr,
        ]);

        if ($request->filled('check_in_time')) {
            $inTime = trim($request->input('check_in_time'));
            $attendance->check_in_at = Carbon::parse($dateStr . ' ' . $inTime);
        }

        if ($request->filled('check_out_time')) {
            $outTime = trim($request->input('check_out_time'));
            $attendance->check_out_at = Carbon::parse($dateStr . ' ' . $outTime);
        }

        // Validate sequence
        if ($attendance->check_in_at && $attendance->check_out_at) {
            if (Carbon::parse($attendance->check_out_at)->lt(Carbon::parse($attendance->check_in_at))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Check-out time cannot be earlier than check-in time.',
                ], 422);
            }
        }

        $reason = trim($request->input('reason'));
        $attendance->is_regularized = true;
        $attendance->regularized_by = $supervisor->employee_id;
        $attendance->regularization_reason = $reason;
        $attendance->status = ($attendance->check_in_at && $attendance->check_out_at) ? 'checked_out' : 'checked_in';
        $attendance->note = "Regularized by supervisor {$supervisor->employee_name}: {$reason}";
        $attendance->save();

        $shiftService = app(ShiftAttendanceService::class);
        $eval = $shiftService->evaluate($attendance->check_in_at, $attendance->check_out_at, $member->shift);

        return response()->json([
            'success' => true,
            'message' => "Attendance for {$member->employee_name} on {$dateStr} regularized successfully.",
            'data' => [
                'employee_id' => $member->employee_id,
                'employee_name' => $member->employee_name,
                'attendance_date' => $dateStr,
                'check_in' => $attendance->check_in_at ? [
                    'time' => Carbon::parse($attendance->check_in_at)->format('h:i A'),
                    'datetime' => Carbon::parse($attendance->check_in_at)->toIso8601String(),
                ] : null,
                'check_out' => $attendance->check_out_at ? [
                    'time' => Carbon::parse($attendance->check_out_at)->format('h:i A'),
                    'datetime' => Carbon::parse($attendance->check_out_at)->toIso8601String(),
                ] : null,
                'is_regularized' => true,
                'regularized_by' => $supervisor->employee_name,
                'regularization_reason' => $reason,
                'shift_evaluation' => [
                    'is_late' => (bool) ($eval['is_late'] ?? false),
                    'late_minutes' => (int) ($eval['late_minutes'] ?? 0),
                    'is_early_out' => (bool) ($eval['is_early_out'] ?? false),
                    'work_duration' => $eval['work_duration'] ?? null,
                ],
            ],
        ], 200);
    }

    /**
     * 12. Apply Leave on Behalf of a Subordinate
     */
    public function applyLeaveForMember(Request $request)
    {
        $supervisor = $this->authorizeSupervisor($request);
        if (!$supervisor) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Supervisor access required.',
            ], 403);
        }

        $request->validate([
            'employee_id' => 'required|integer',
            'leave_type_id' => 'required|exists:leave_types,id',
            'from_date' => 'required|date_format:Y-m-d',
            'to_date' => 'required|date_format:Y-m-d|after_or_equal:from_date',
            'reason' => 'required|string|max:2000',
            'auto_approve' => 'nullable|boolean',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $teamMemberIds = $supervisor->teamQuery()->pluck('employee_id')->toArray();
        $targetEmployeeId = (int) $request->input('employee_id');

        if (!in_array($targetEmployeeId, $teamMemberIds)) {
            return response()->json([
                'success' => false,
                'message' => 'The selected employee is not in your supervised team.',
            ], 404);
        }

        $member = Employee::with(['designation', 'shift'])->findOrFail($targetEmployeeId);
        $fromDate = Carbon::parse($request->input('from_date'));
        $toDate = Carbon::parse($request->input('to_date'));
        $duration = $fromDate->diffInDays($toDate) + 1;

        $leaveType = LeaveType::where('id', $request->input('leave_type_id'))
            ->where('status', 'Active')
            ->first();

        if (!$leaveType) {
            return response()->json([
                'success' => false,
                'message' => 'Selected leave type is not active or invalid.',
            ], 422);
        }

        // Restricted Holiday (RH) conditional checks
        $isRestricted = str_contains(strtolower($leaveType->leave_name), 'restricted')
            || strcasecmp($leaveType->leave_code, 'RH-001') === 0;

        $holidayId = null;
        if ($isRestricted) {
            if ($fromDate->toDateString() !== $toDate->toDateString()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Restricted Holiday leave can only be applied for a single day on the designated holiday date.',
                ], 422);
            }

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
                    'message' => "Office is officially closed on {$holiday->holiday_name} ({$fromDate->format('d M, Y')}) as a Common Mandatory Holiday.",
                ], 422);
            }

            $alreadyApplied = Leave::where('employee_id', $member->employee_id)
                ->where('holiday_id', $holiday->id)
                ->whereIn('status', ['DRAFT', 'SENT', 'APPROVED'])
                ->exists();

            if ($alreadyApplied) {
                return response()->json([
                    'success' => false,
                    'message' => "Employee has already submitted a leave request for {$holiday->holiday_name}.",
                ], 422);
            }

            $holidayId = $holiday->id;
        }

        // Check overlapping leaves
        $overlapping = Leave::where('employee_id', $member->employee_id)
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
                'message' => 'Employee already has an active or pending leave overlapping with these dates.',
            ], 422);
        }

        $autoApprove = filter_var($request->input('auto_approve', true), FILTER_VALIDATE_BOOLEAN);
        $remarks = trim($request->input('remarks', 'Leave applied by supervisor on behalf of employee via mobile app.'));

        $userObj = User::where('email', $supervisor->employee_email)->first();
        $userId = $userObj?->id ?? null;

        $leave = Leave::create([
            'employee_id' => $member->employee_id,
            'leave_type_id' => $leaveType->id,
            'holiday_id' => $holidayId,
            'from_date' => $fromDate->toDateString(),
            'to_date' => $toDate->toDateString(),
            'duration' => $duration,
            'reason' => trim($request->input('reason')),
            'status' => $autoApprove ? 'APPROVED' : 'SENT',
            'approved_by' => $autoApprove ? $userId : null,
            'supervisor_remarks' => $remarks,
            'action_at' => $autoApprove ? Carbon::now() : null,
        ]);

        LeaveActivityLog::create([
            'leave_id' => $leave->id,
            'actor_type' => 'Supervisor',
            'actor_id' => $supervisor->employee_id,
            'actor_name' => $supervisor->employee_name,
            'action' => $autoApprove ? 'applied_and_approved' : 'applied_on_behalf',
            'remarks' => $remarks,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => Carbon::now(),
        ]);

        if ($autoApprove) {
            $this->dispatchLeaveEmail($leave, 'Approved', $supervisor->employee_name, $remarks);
        } else {
            \App\Services\LeaveNotificationService::notifyLeaveApplied($leave);
        }

        return response()->json([
            'success' => true,
            'message' => "Leave for {$member->employee_name} created successfully " . ($autoApprove ? 'and approved.' : 'as pending.'),
            'data' => $this->formatTeamLeave($leave->fresh(['employee.designation', 'leaveType', 'holiday', 'activityLogs'])),
        ], 201);
    }

    /**
     * 13. Team Turnout & Monthly Analytics Summary
     */
    public function teamMonthlySummary(Request $request)
    {
        $supervisor = $this->authorizeSupervisor($request);
        if (!$supervisor) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Supervisor access required.',
            ], 403);
        }

        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = (clone $startDate)->endOfMonth();
        $daysInMonth = $endDate->day;

        $teamMembers = $supervisor->teamQuery()->with(['designation', 'shift', 'agency'])->get();
        $teamMemberIds = $teamMembers->pluck('employee_id')->toArray();
        $totalMembers = count($teamMembers);

        $attendances = EmployeeAttendance::whereIn('employee_id', $teamMemberIds)
            ->whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->groupBy('employee_id');

        $approvedLeaves = Leave::whereIn('employee_id', $teamMemberIds)
            ->where('status', 'APPROVED')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('from_date', [$startDate->toDateString(), $endDate->toDateString()])
                  ->orWhereBetween('to_date', [$startDate->toDateString(), $endDate->toDateString()]);
            })
            ->get()
            ->groupBy('employee_id');

        $shiftService = app(ShiftAttendanceService::class);

        $cumulativePresent = 0;
        $cumulativeLate = 0;
        $cumulativeLeaves = 0;

        $memberSummaries = [];

        foreach ($teamMembers as $m) {
            $mAtts = $attendances->get($m->employee_id, collect());
            $mLeaves = $approvedLeaves->get($m->employee_id, collect());

            $presentCount = $mAtts->whereNotNull('check_in_at')->count();
            $lateCount = 0;

            foreach ($mAtts as $att) {
                if ($att->check_in_at) {
                    $eval = $shiftService->evaluate($att->check_in_at, $att->check_out_at, $m->shift);
                    if (!empty($eval['is_late'])) {
                        $lateCount++;
                    }
                }
            }

            $leaveDays = 0;
            foreach ($mLeaves as $l) {
                $leaveDays += $l->duration ?: 1;
            }

            $cumulativePresent += $presentCount;
            $cumulativeLate += $lateCount;
            $cumulativeLeaves += $leaveDays;

            $rate = $daysInMonth > 0 ? round(($presentCount / $daysInMonth) * 100, 1) : 0;

            $memberSummaries[] = [
                'employee_id' => $m->employee_id,
                'employee_name' => $m->employee_name,
                'designation' => $m->designation?->name ?? 'Staff Member',
                'agency_name' => $m->agency?->agency_name ?? 'Direct / In-House',
                'present_days' => $presentCount,
                'late_days' => $lateCount,
                'leave_days' => $leaveDays,
                'attendance_percentage' => $rate,
            ];
        }

        $totalPossibleManDays = $totalMembers * $daysInMonth;
        $averageAttendanceRate = $totalPossibleManDays > 0 ? round(($cumulativePresent / $totalPossibleManDays) * 100, 1) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'month' => $month,
                'month_name' => $startDate->format('F Y'),
                'year' => $year,
                'team_overview' => [
                    'total_team_members' => $totalMembers,
                    'days_in_month' => $daysInMonth,
                    'total_present_mandays' => $cumulativePresent,
                    'total_late_incidents' => $cumulativeLate,
                    'total_leave_mandays' => $cumulativeLeaves,
                    'average_team_turnout_percentage' => $averageAttendanceRate,
                ],
                'member_breakdown' => $memberSummaries,
            ],
        ], 200);
    }

    /**
     * Helper to compute leave balances for a specific employee.
     */
    protected function calculateEmployeeLeaveBalances(Employee $employee, int $year): array
    {
        $mappingsByTypeId = collect();
        if ($employee->designation_id) {
            $mappingsByTypeId = LeaveMapping::where('designation_id', $employee->designation_id)
                ->where('status', 'Active')
                ->get()
                ->keyBy('leave_type_id');
        }

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

        $types = LeaveType::where('status', 'Active')->orderBy('id')->get();
        $balances = [];

        foreach ($types as $t) {
            $allottedDays = $mappingsByTypeId->has($t->id)
                ? (int) $mappingsByTypeId->get($t->id)->allow_days
                : (int) ($t->total_leaves ?? 0);

            $usedDays = 0;
            if ($approvedLeavesUsed->has($t->id)) {
                $usedDays = $approvedLeavesUsed->get($t->id)->sum(function ($l) {
                    return $l->duration ?: (Carbon::parse($l->from_date)->diffInDays(Carbon::parse($l->to_date)) + 1);
                });
            }

            $pendingDays = 0;
            if ($pendingLeaves->has($t->id)) {
                $pendingDays = $pendingLeaves->get($t->id)->sum(function ($l) {
                    return $l->duration ?: (Carbon::parse($l->from_date)->diffInDays(Carbon::parse($l->to_date)) + 1);
                });
            }

            $remainingDays = max($allottedDays - $usedDays, 0);

            $balances[] = [
                'leave_type_id' => $t->id,
                'leave_name' => $t->leave_name,
                'leave_code' => $t->leave_code,
                'allotted_days' => $allottedDays,
                'used_days' => $usedDays,
                'pending_days' => $pendingDays,
                'remaining_days' => $remainingDays,
                'is_restricted_holiday' => str_contains(strtolower($t->leave_name), 'restricted') || $t->leave_code === 'RH-001',
            ];
        }

        return $balances;
    }

    /**
     * Helper: Format Team Leave item.
     */
    protected function formatTeamLeave(Leave $leave): array
    {
        $statusLabels = [
            'DRAFT' => 'Draft',
            'SENT' => 'Pending Approval',
            'APPROVED' => 'Approved',
            'REJECTED' => 'Rejected',
        ];

        return [
            'id' => $leave->id,
            'employee' => [
                'id' => $leave->employee?->employee_id,
                'name' => $leave->employee?->employee_name ?? 'Unknown Staff',
                'email' => $leave->employee?->employee_email,
                'mobile' => $leave->employee?->employee_mobile,
                'designation' => $leave->employee?->designation?->name ?? 'Staff Member',
                'profile_image' => $leave->employee?->profile_image ? asset('storage/' . $leave->employee->profile_image) : null,
            ],
            'leave_type' => [
                'id' => $leave->leave_type_id,
                'name' => $leave->leaveType?->leave_name ?? $leave->leaveType?->name ?? 'General Leave',
                'leave_name' => $leave->leaveType?->leave_name ?? $leave->leaveType?->name ?? 'General Leave',
                'code' => $leave->leaveType?->leave_code,
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
            'is_pending' => in_array($leave->status, ['SENT', 'DRAFT', 'PENDING']),
            'supervisor_remarks' => $leave->supervisor_remarks,
            'action_at' => optional($leave->action_at)->toDateTimeString(),
            'action_by' => $leave->approver?->name ?? $leave->rejecter?->name ?? null,
            'created_at' => optional($leave->created_at)->toDateTimeString(),
            'timeline_activity' => $leave->relationLoaded('activityLogs') ? $leave->activityLogs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'actor_name' => $log->actor_name,
                    'actor_type' => $log->actor_type,
                    'action' => $log->action,
                    'remarks' => $log->remarks,
                    'created_at' => optional($log->created_at)->toDateTimeString(),
                ];
            }) : [],
        ];
    }

    /**
     * Helper: Dispatch leave status change email.
     */
    protected function dispatchLeaveEmail(Leave $leave, string $status, ?string $actorName = null, ?string $reason = null)
    {
        \App\Services\LeaveNotificationService::notifyLeaveStatusChanged($leave, $status, $actorName, $reason);
    }

    /**
     * 13. Reports Hub / Summary Dashboard
     * Provides an overview of team attendance, task progress, and leave statistics.
     */
    public function reportSummary(Request $request)
    {
        $supervisor = $this->authorizeSupervisor($request);
        if (!$supervisor) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Supervisor access required.',
            ], 403);
        }

        $now = Carbon::now('Asia/Kolkata');
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->input('start_date'), 'Asia/Kolkata')->startOfDay()
            : (clone $now)->startOfMonth();
        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->input('end_date'), 'Asia/Kolkata')->endOfDay()
            : (clone $now)->endOfDay();

        $teamMembers = $supervisor->teamQuery()->with(['designation', 'agency', 'shift'])->get();
        $teamMemberIds = $teamMembers->pluck('employee_id')->toArray();
        $totalMembers = count($teamMemberIds);

        // Attendance stats
        $attendances = EmployeeAttendance::whereIn('employee_id', $teamMemberIds)
            ->whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        $totalPresentPunches = $attendances->whereNotNull('check_in_at')->count();
        $totalWorkedSeconds = $attendances->sum('worked_seconds');
        $shiftService = app(ShiftAttendanceService::class);

        $lateCount = 0;
        $earlyInCount = 0;
        $earlyOutCount = 0;

        foreach ($attendances as $att) {
            $emp = $teamMembers->firstWhere('employee_id', $att->employee_id);
            if ($att->check_in_at) {
                $eval = $shiftService->evaluate($att->check_in_at, $att->check_out_at, $emp?->shift, optional($att->attendance_date)->toDateString());
                if (!empty($eval['is_late'])) $lateCount++;
                if (!empty($eval['is_early_in'])) $earlyInCount++;
                if (!empty($eval['is_early_out'])) $earlyOutCount++;
            }
        }

        // Daily task reports stats
        $dailyReports = DailyReport::whereIn('EmpId', $teamMemberIds)
            ->whereBetween('DrEntryDate', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        $totalDailyReports = $dailyReports->count();
        $totalProjectHoursLogged = $dailyReports->sum(function ($r) {
            return $this->parseHoursStringToDecimal($r->TotalHours);
        });

        // Leaves stats
        $leaves = Leave::whereIn('employee_id', $teamMemberIds)
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('from_date', [$startDate->toDateString(), $endDate->toDateString()])
                  ->orWhereBetween('to_date', [$startDate->toDateString(), $endDate->toDateString()]);
            })
            ->get();

        $approvedLeaves = $leaves->where('status', 'APPROVED');
        $pendingLeaves = $leaves->whereIn('status', ['SENT', 'PENDING', 'DRAFT']);
        $rejectedLeaves = $leaves->where('status', 'REJECTED');

        $totalLeaveDays = $approvedLeaves->sum(function ($l) {
            return $l->duration ?: (Carbon::parse($l->from_date)->diffInDays(Carbon::parse($l->to_date)) + 1);
        });

        $daysInRange = $startDate->diffInDays($endDate) + 1;
        $totalExpectedMandays = $totalMembers * $daysInRange;
        $attendancePercentage = $totalExpectedMandays > 0 ? round(($totalPresentPunches / $totalExpectedMandays) * 100, 1) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'period' => [
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'days_in_range' => $daysInRange,
                ],
                'team_overview' => [
                    'total_members' => $totalMembers,
                    'total_expected_mandays' => $totalExpectedMandays,
                    'present_mandays' => $totalPresentPunches,
                    'turnout_percentage' => $attendancePercentage,
                    'late_arrivals' => $lateCount,
                    'early_arrivals' => $earlyInCount,
                    'early_departures' => $earlyOutCount,
                    'total_hours_worked' => round($totalWorkedSeconds / 3600, 2),
                    'average_daily_hours_per_member' => $totalPresentPunches > 0 ? round(($totalWorkedSeconds / 3600) / $totalPresentPunches, 2) : 0,
                ],
                'daily_work_reports' => [
                    'total_submissions' => $totalDailyReports,
                    'total_hours_logged' => round($totalProjectHoursLogged, 2),
                ],
                'leaves_summary' => [
                    'total_applications' => $leaves->count(),
                    'approved_applications' => $approvedLeaves->count(),
                    'approved_leave_days' => $totalLeaveDays,
                    'pending_applications' => $pendingLeaves->count(),
                    'rejected_applications' => $rejectedLeaves->count(),
                ],
                'available_reports' => [
                    [
                        'name' => 'Attendance Punch Logs Report',
                        'endpoint' => '/api/v1/employee/supervisor/reports/attendance',
                        'method' => 'GET',
                        'description' => 'Detailed punch-in/out logs with shift evaluation, punctuality, and working hours.',
                    ],
                    [
                        'name' => 'Monthly Attendance Register Matrix',
                        'endpoint' => '/api/v1/employee/supervisor/reports/attendance-register',
                        'method' => 'GET',
                        'description' => 'Calendar grid (1-31) of daily attendance statuses for all subordinates.',
                    ],
                    [
                        'name' => 'Daily Work & Project Task Reports',
                        'endpoint' => '/api/v1/employee/supervisor/reports/daily-work-reports',
                        'method' => 'GET',
                        'description' => 'Task descriptions, logged project hours, and daily submissions.',
                    ],
                    [
                        'name' => 'Team Leave Utilization Report',
                        'endpoint' => '/api/v1/employee/supervisor/reports/leaves',
                        'method' => 'GET',
                        'description' => 'Subordinate leave balances, approved history, and application trends.',
                    ],
                ],
            ],
        ], 200);
    }

    /**
     * 14. Team Attendance Detailed Report
     * Detailed punch records for supervised subordinates with shift evaluations, duration, and punctuality.
     */
    public function attendanceReport(Request $request)
    {
        $supervisor = $this->authorizeSupervisor($request);
        if (!$supervisor) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Supervisor access required.',
            ], 403);
        }

        $request->validate([
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
            'employee_id' => 'nullable|integer',
            'status' => 'nullable|string|in:ALL,PRESENT,ABSENT,LATE,EARLY_IN,ON_LEAVE,HALF_DAY,CHECKED_IN,CHECKED_OUT',
            'per_page' => 'nullable|integer|min:1|max:100',
            'export' => 'nullable|string|in:excel,pdf',
        ]);

        if ($request->input('export') === 'excel') {
            return $this->exportAttendanceLogExcel($request);
        }
        if ($request->input('export') === 'pdf') {
            return $this->exportAttendanceLogPdf($request);
        }

        $teamMemberIds = $supervisor->teamQuery()->pluck('employee_id')->toArray();
        if ($request->filled('employee_id')) {
            $reqEmpId = (int) $request->input('employee_id');
            if (!in_array($reqEmpId, $teamMemberIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee does not belong to your team.',
                ], 404);
            }
            $targetEmpIds = [$reqEmpId];
        } else {
            $targetEmpIds = $teamMemberIds;
        }

        $now = Carbon::now('Asia/Kolkata');
        $startDate = $request->input('start_date', (clone $now)->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', $now->toDateString());

        $query = EmployeeAttendance::with(['employee.designation', 'employee.agency', 'employee.shift'])
            ->whereIn('employee_id', $targetEmpIds)
            ->whereBetween('attendance_date', [$startDate, $endDate]);

        if ($request->filled('status') && strtoupper($request->status) !== 'ALL') {
            $st = strtoupper($request->status);
            if ($st === 'CHECKED_IN') {
                $query->where(function ($q) {
                    $q->where('status', 'checked_in')->orWhereNull('check_out_at');
                });
            } elseif ($st === 'CHECKED_OUT') {
                $query->where(function ($q) {
                    $q->where('status', 'checked_out')->orWhereNotNull('check_out_at');
                });
            } elseif (in_array($st, ['PRESENT', 'ABSENT', 'HALF_DAY'])) {
                $query->where('status', strtolower($st));
            }
        }

        $perPage = (int) $request->input('per_page', 25);
        $attendances = $query->orderBy('attendance_date', 'desc')->orderBy('check_in_at', 'desc')->paginate($perPage);

        $shiftService = app(ShiftAttendanceService::class);

        $records = $attendances->map(function ($att) use ($shiftService) {
            $emp = $att->employee;
            $shift = $emp?->shift;
            $eval = $shiftService->evaluate($att->check_in_at, $att->check_out_at, $shift, optional($att->attendance_date)->toDateString());

            $checkIn = $att->getCheckInIST();
            $checkOut = $att->getCheckOutIST();

            $status = 'PRESENT';
            if (!empty($eval['is_late'])) {
                $status = 'LATE';
            } elseif (!empty($eval['is_early_in'])) {
                $status = 'EARLY_IN';
            }

            return [
                'attendance_id' => $att->id,
                'employee' => [
                    'id' => $emp?->employee_id,
                    'name' => $emp?->employee_name,
                    'designation' => $emp?->designation?->name ?? 'Staff Member',
                    'agency' => $emp?->agency?->agency_name ?? 'Direct',
                    'profile_image' => $emp?->profile_image ? asset('storage/' . $emp->profile_image) : null,
                ],
                'date' => optional($att->attendance_date)->toDateString(),
                'day' => optional($att->attendance_date)->format('l'),
                'check_in_at' => optional($checkIn)->toDateTimeString(),
                'check_in_time' => optional($checkIn)->format('h:i A'),
                'check_out_at' => optional($checkOut)->toDateTimeString(),
                'check_out_time' => optional($checkOut)->format('h:i A'),
                'status' => $status,
                'raw_status' => $att->status,
                'shift' => [
                    'name' => $shift?->shift_name ?? 'General Shift',
                    'start_time' => $shift?->start_time ?? '09:30:00',
                    'end_time' => $shift?->end_time ?? '18:30:00',
                ],
                'punctuality' => [
                    'is_late' => (bool) ($eval['is_late'] ?? false),
                    'late_minutes' => (int) ($eval['late_minutes'] ?? 0),
                    'late_formatted' => $eval['late_formatted'] ?? null,
                    'is_early_in' => (bool) ($eval['is_early_in'] ?? false),
                    'early_in_minutes' => (int) ($eval['early_in_minutes'] ?? 0),
                    'is_early_out' => (bool) ($eval['is_early_out'] ?? false),
                    'early_out_minutes' => (int) ($eval['early_out_minutes'] ?? 0),
                ],
                'working_seconds' => $att->worked_seconds,
                'working_clock' => $att->worked_clock,
                'working_formatted' => $att->worked_formatted,
                'timer' => $att->getTimerPayload(),
                'notes' => $att->note,
            ];
        });

        // Summary calculations
        $totalWorkedSecs = $attendances->sum('worked_seconds');
        $totalLateCount = 0;
        $totalEarlyCount = 0;

        foreach ($attendances as $att) {
            $emp = $att->employee;
            if ($att->check_in_at) {
                $eval = $shiftService->evaluate($att->check_in_at, $att->check_out_at, $emp?->shift, optional($att->attendance_date)->toDateString());
                if (!empty($eval['is_late'])) $totalLateCount++;
                if (!empty($eval['is_early_in'])) $totalEarlyCount++;
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'records' => $records,
                'pagination' => [
                    'current_page' => $attendances->currentPage(),
                    'last_page' => $attendances->lastPage(),
                    'per_page' => $attendances->perPage(),
                    'total_records' => $attendances->total(),
                ],
                'summary' => [
                    'total_records' => $attendances->total(),
                    'total_hours_worked' => round($totalWorkedSecs / 3600, 2),
                    'late_arrivals_in_page' => $totalLateCount,
                    'early_arrivals_in_page' => $totalEarlyCount,
                ],
                'filters_applied' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'employee_id' => $request->employee_id,
                    'status' => $request->status ?? 'ALL',
                ],
            ],
        ], 200);
    }

    /**
     * Export Team Attendance Logs to Excel.
     */
    public function exportAttendanceLogExcel(Request $request)
    {
        $supervisor = $this->authorizeSupervisor($request);
        if (!$supervisor) {
            return response()->json(['success' => false, 'message' => 'Unauthorized: Supervisor access required.'], 403);
        }

        $data = $this->buildAttendanceLogData($supervisor, $request);
        $fileName = 'Team_Attendance_Logs_' . $supervisor->employee_id . '_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new SupervisorAttendanceLogExport($data), $fileName);
    }

    /**
     * Export Team Attendance Logs to PDF.
     */
    public function exportAttendanceLogPdf(Request $request)
    {
        $supervisor = $this->authorizeSupervisor($request);
        if (!$supervisor) {
            return response()->json(['success' => false, 'message' => 'Unauthorized: Supervisor access required.'], 403);
        }

        $data = $this->buildAttendanceLogData($supervisor, $request);
        $fileName = 'Team_Attendance_Logs_' . $supervisor->employee_id . '_' . now()->format('Ymd_His') . '.pdf';
        $pdf = Pdf::loadView('supervisor.reports.attendance_log_pdf', $data)
            ->setPaper('a4', 'landscape');
        return $pdf->download($fileName);
    }

    /**
     * Helper to aggregate attendance punch logs data for web, PDF, and Excel.
     */
    public function buildAttendanceLogData($supervisor, Request $request): array
    {
        $now = Carbon::now('Asia/Kolkata');
        $startDate = $request->input('start_date', (clone $now)->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', $now->toDateString());

        $teamMemberIds = $supervisor->teamQuery()->pluck('employee_id')->toArray();
        if ($request->filled('employee_id')) {
            $reqEmpId = (int) $request->input('employee_id');
            if (in_array($reqEmpId, $teamMemberIds)) {
                $teamMemberIds = [$reqEmpId];
            }
        }

        $query = EmployeeAttendance::with(['employee.designation', 'employee.agency', 'employee.shift'])
            ->whereIn('employee_id', $teamMemberIds)
            ->whereBetween('attendance_date', [$startDate, $endDate]);

        if ($request->filled('status') && strtoupper($request->status) !== 'ALL') {
            $st = strtoupper($request->status);
            if ($st === 'PRESENT') {
                $query->whereNotNull('check_in_at');
            } elseif ($st === 'CHECKED_IN') {
                $query->whereNotNull('check_in_at')->whereNull('check_out_at');
            } else {
                $query->where('status', strtolower($st));
            }
        }

        $attendances = $query->orderBy('attendance_date', 'desc')->orderBy('check_in_at', 'desc')->get();
        $shiftService = app(ShiftAttendanceService::class);

        $records = [];
        $totalWorkedSecs = 0;
        $totalLateCount = 0;
        $totalEarlyCount = 0;

        foreach ($attendances as $att) {
            $emp = $att->employee;
            $shift = $emp?->shift;
            $eval = $shiftService->evaluate($att->check_in_at, $att->check_out_at, $shift, optional($att->attendance_date)->toDateString());

            $checkIn = $att->getCheckInIST();
            $checkOut = $att->getCheckOutIST();

            $status = 'PRESENT';
            if (!empty($eval['is_late'])) {
                $status = 'LATE';
                $totalLateCount++;
            } elseif (!empty($eval['is_early_in'])) {
                $status = 'EARLY_IN';
                $totalEarlyCount++;
            }

            $totalWorkedSecs += (int) $att->worked_seconds;

            $records[] = [
                'attendance_id' => $att->id,
                'employee' => [
                    'id' => $emp?->employee_id,
                    'name' => $emp?->employee_name,
                    'designation' => $emp?->designation?->name ?? 'Staff Member',
                    'agency' => $emp?->agency?->agency_name ?? 'Direct',
                ],
                'date' => optional($att->attendance_date)->toDateString(),
                'day' => optional($att->attendance_date)->format('l'),
                'check_in_time' => optional($checkIn)->format('h:i A'),
                'check_out_time' => optional($checkOut)->format('h:i A'),
                'status' => $status,
                'punctuality' => [
                    'is_late' => (bool) ($eval['is_late'] ?? false),
                    'late_formatted' => $eval['late_formatted'] ?? null,
                    'is_early_in' => (bool) ($eval['is_early_in'] ?? false),
                    'early_in_minutes' => (int) ($eval['early_in_minutes'] ?? 0),
                ],
                'working_formatted' => $att->worked_formatted,
                'notes' => $att->note,
            ];
        }

        $summary = [
            'total_records' => count($records),
            'total_hours_worked' => round($totalWorkedSecs / 3600, 2),
            'late_arrivals' => $totalLateCount,
            'early_arrivals' => $totalEarlyCount,
        ];

        return [
            'supervisor' => $supervisor,
            'periodText' => Carbon::parse($startDate)->format('d M, Y') . ' - ' . Carbon::parse($endDate)->format('d M, Y'),
            'records' => $records,
            'summary' => $summary,
            'companyDetails' => Company::first(),
        ];
    }

    /**
     * Export Team Attendance Register Matrix to Excel (same engine as Admin).
     */
    public function exportAttendanceRegisterExcel(Request $request)
    {
        $supervisor = $this->authorizeSupervisor($request);
        if (!$supervisor) {
            return response()->json(['success' => false, 'message' => 'Unauthorized: Supervisor access required.'], 403);
        }

        $request->merge([
            'supervisor_id' => $supervisor->employee_id,
            'attendance_scope' => $request->input('attendance_scope', 'all'),
        ]);

        return app(AttendanceRegisterReportController::class)->exportExcel($request);
    }

    /**
     * Export Team Attendance Register Matrix to PDF (same engine as Admin).
     */
    public function exportAttendanceRegisterPdf(Request $request)
    {
        $supervisor = $this->authorizeSupervisor($request);
        if (!$supervisor) {
            return response()->json(['success' => false, 'message' => 'Unauthorized: Supervisor access required.'], 403);
        }

        $request->merge([
            'supervisor_id' => $supervisor->employee_id,
            'attendance_scope' => $request->input('attendance_scope', 'all'),
        ]);

        return app(AttendanceRegisterReportController::class)->exportPdf($request);
    }

    /**
     * 15. Monthly Attendance Register Matrix
     * Comprehensive Day 1..31 calendar grid for all supervised subordinates with status badges.
     */
    public function attendanceRegisterReport(Request $request)
    {
        $supervisor = $this->authorizeSupervisor($request);
        if (!$supervisor) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Supervisor access required.',
            ], 403);
        }

        if ($request->input('export') === 'excel') {
            return $this->exportAttendanceRegisterExcel($request);
        }
        if ($request->input('export') === 'pdf') {
            return $this->exportAttendanceRegisterPdf($request);
        }

        $month = (int) $request->input('month', Carbon::now('Asia/Kolkata')->month);
        $year = (int) $request->input('year', Carbon::now('Asia/Kolkata')->year);

        $startDate = Carbon::createFromDate($year, $month, 1, 'Asia/Kolkata')->startOfDay();
        $endDate = (clone $startDate)->endOfMonth()->startOfDay();
        $daysInMonth = $startDate->daysInMonth;
        $now = Carbon::now('Asia/Kolkata');

        $teamQuery = $supervisor->teamQuery()->with(['designation', 'agency', 'shift']);
        if ($request->filled('employee_id')) {
            $teamQuery->where('employee_id', (int) $request->input('employee_id'));
        }
        $teamMembers = $teamQuery->orderBy('employee_name')->get();
        $teamMemberIds = $teamMembers->pluck('employee_id')->toArray();

        // Attendance data
        $attendances = EmployeeAttendance::whereIn('employee_id', $teamMemberIds)
            ->whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->groupBy('employee_id');

        // Leaves data
        $leaves = Leave::with('leaveType')
            ->whereIn('employee_id', $teamMemberIds)
            ->where('status', 'APPROVED')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('from_date', [$startDate->toDateString(), $endDate->toDateString()])
                  ->orWhereBetween('to_date', [$startDate->toDateString(), $endDate->toDateString()]);
            })
            ->get()
            ->groupBy('employee_id');

        // Holidays in month
        $holidays = Holiday::where('status', 1)
            ->whereBetween('holiday_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->keyBy(function ($h) {
                return Carbon::parse($h->holiday_date)->format('Y-m-d');
            });

        $shiftService = app(ShiftAttendanceService::class);

        // Build day column headers
        $daysHeader = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $curDate = Carbon::createFromDate($year, $month, $d, 'Asia/Kolkata');
            $dateStr = $curDate->toDateString();
            $isSunday = $curDate->isSunday();
            $holiday = $holidays->get($dateStr);

            $daysHeader[] = [
                'day' => $d,
                'date' => $dateStr,
                'day_name' => $curDate->format('D'),
                'is_sunday' => $isSunday,
                'is_holiday' => (bool) $holiday,
                'holiday_name' => $holiday?->holiday_name,
                'is_future' => $curDate->isAfter($now->endOfDay()),
            ];
        }

        // Build member grid
        $matrix = [];
        $totalPresentMandays = 0;

        foreach ($teamMembers as $m) {
            $mAtts = $attendances->get($m->employee_id, collect())->keyBy(function ($a) {
                return optional($a->attendance_date)->toDateString();
            });

            $mLeaves = $leaves->get($m->employee_id, collect());

            $daysMap = [];
            $presentDays = 0;
            $lateDays = 0;
            $leaveDays = 0;
            $absentDays = 0;
            $totalWorkedSeconds = 0;

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $curDate = Carbon::createFromDate($year, $month, $d, 'Asia/Kolkata');
                $dateStr = $curDate->toDateString();
                $isSunday = $curDate->isSunday();
                $isFuture = $curDate->isAfter($now->endOfDay());
                $holiday = $holidays->get($dateStr);

                $att = $mAtts->get($dateStr);
                $hasLeave = $mLeaves->first(function ($l) use ($dateStr) {
                    return $l->from_date <= $dateStr && $l->to_date >= $dateStr;
                });

                $status = '-';
                $code = '-';
                $checkInTime = null;
                $checkOutTime = null;
                $workedHours = 0;

                if ($att && $att->check_in_at) {
                    $eval = $shiftService->evaluate($att->check_in_at, $att->check_out_at, $m->shift, $dateStr);
                    $checkInTime = optional($att->getCheckInIST())->format('h:i A');
                    $checkOutTime = optional($att->getCheckOutIST())->format('h:i A');
                    $workedSeconds = $att->worked_seconds;
                    $totalWorkedSeconds += $workedSeconds;
                    $workedHours = round($workedSeconds / 3600, 2);

                    $presentDays++;
                    if (!empty($eval['is_late'])) {
                        $code = 'L';
                        $status = 'Late';
                        $lateDays++;
                    } else {
                        $code = 'P';
                        $status = 'Present';
                    }
                } elseif ($hasLeave) {
                    $code = 'LV';
                    $status = 'Leave (' . ($hasLeave->leaveType?->leave_code ?? 'Approved') . ')';
                    $leaveDays++;
                } elseif ($holiday) {
                    $code = 'H';
                    $status = 'Holiday (' . $holiday->holiday_name . ')';
                } elseif ($isSunday) {
                    $code = 'WO';
                    $status = 'Weekly Off';
                } elseif (!$isFuture) {
                    $code = 'A';
                    $status = 'Absent';
                    $absentDays++;
                } else {
                    $code = '-';
                    $status = 'Upcoming';
                }

                $daysMap[(string) $d] = [
                    'code' => $code,
                    'status' => $status,
                    'check_in' => $checkInTime,
                    'check_out' => $checkOutTime,
                    'worked_hours' => $workedHours,
                ];
            }

            $totalPresentMandays += $presentDays;

            $matrix[] = [
                'employee' => [
                    'id' => $m->employee_id,
                    'name' => $m->employee_name,
                    'designation' => $m->designation?->name ?? 'Staff Member',
                    'agency' => $m->agency?->agency_name ?? 'Direct',
                    'profile_image' => $m->profile_image ? asset('storage/' . $m->profile_image) : null,
                ],
                'summary' => [
                    'present_days' => $presentDays,
                    'absent_days' => $absentDays,
                    'leave_days' => $leaveDays,
                    'late_days' => $lateDays,
                    'total_hours' => round($totalWorkedSeconds / 3600, 2),
                    'attendance_percentage' => $daysInMonth > 0 ? round(($presentDays / $daysInMonth) * 100, 1) : 0,
                ],
                'days' => $daysMap,
            ];
        }

        $totalPossibleMandays = count($teamMembers) * $daysInMonth;
        $overallTurnout = $totalPossibleMandays > 0 ? round(($totalPresentMandays / $totalPossibleMandays) * 100, 1) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'month' => $month,
                'month_name' => $startDate->format('F Y'),
                'year' => $year,
                'days_in_month' => $daysInMonth,
                'days_headers' => $daysHeader,
                'total_team_members' => count($teamMembers),
                'overall_turnout_percentage' => $overallTurnout,
                'status_legend' => [
                    'P' => 'Present (On Time)',
                    'L' => 'Late In',
                    'A' => 'Absent',
                    'LV' => 'Approved Leave',
                    'WO' => 'Weekly Off (Sunday)',
                    'H' => 'Public Holiday',
                    '-' => 'Upcoming / Off',
                ],
                'register' => $matrix,
            ],
        ], 200);
    }

    /**
     * 16. Subordinates' Daily Work / Task Progress Reports
     * Work descriptions and project hours logged by subordinates from dailyreport table.
     */
    public function dailyWorkReports(Request $request)
    {
        $supervisor = $this->authorizeSupervisor($request);
        if (!$supervisor) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Supervisor access required.',
            ], 403);
        }

        $request->validate([
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
            'employee_id' => 'nullable|integer',
            'project_id' => 'nullable|integer',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $teamMemberIds = $supervisor->teamQuery()->pluck('employee_id')->toArray();
        if ($request->filled('employee_id')) {
            $reqEmpId = (int) $request->input('employee_id');
            if (!in_array($reqEmpId, $teamMemberIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee does not belong to your team.',
                ], 404);
            }
            $targetEmpIds = [$reqEmpId];
        } else {
            $targetEmpIds = $teamMemberIds;
        }

        $now = Carbon::now('Asia/Kolkata');
        $startDate = $request->input('start_date', (clone $now)->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', $now->toDateString());

        $query = DailyReport::with(['employeeDetails.designation', 'projectDetails'])
            ->whereIn('EmpId', $targetEmpIds)
            ->whereBetween('DrEntryDate', [$startDate, $endDate]);

        if ($request->filled('project_id')) {
            $query->where('ProjectID', (int) $request->input('project_id'));
        }

        $perPage = (int) $request->input('per_page', 20);
        $reports = $query->orderBy('DrEntryDate', 'desc')->orderBy('report_ID', 'desc')->paginate($perPage);

        $records = $reports->map(function ($r) {
            $emp = $r->employeeDetails;
            $proj = $r->projectDetails;

            return [
                'report_id' => $r->report_ID,
                'employee' => [
                    'id' => $emp?->employee_id,
                    'name' => $emp?->employee_name ?? 'Unknown Staff',
                    'designation' => $emp?->designation?->name ?? 'Staff Member',
                ],
                'project' => [
                    'id' => $proj?->project_id ?? $r->ProjectID,
                    'name' => $proj?->project_name ?? 'General / Non-Project',
                ],
                'entry_date' => optional($r->DrEntryDate)->toDateString(),
                'starting_time' => optional($r->StartingTime)->format('H:i'),
                'ending_time' => optional($r->EndingTime)->format('H:i'),
                'total_hours' => $this->parseHoursStringToDecimal($r->TotalHours),
                'total_hours_raw' => (string) $r->TotalHours,
                'work_description' => $r->WorkDescription,
                'submitted_at' => optional($r->CreationDate)->toDateTimeString(),
            ];
        });

        // Summary calculations
        $totalHoursLogged = $reports->sum(function ($r) {
            return $this->parseHoursStringToDecimal($r->TotalHours);
        });
        $activeContributors = $reports->pluck('EmpId')->unique()->count();
        $uniqueProjects = $reports->pluck('ProjectID')->unique()->count();

        return response()->json([
            'success' => true,
            'data' => [
                'records' => $records,
                'pagination' => [
                    'current_page' => $reports->currentPage(),
                    'last_page' => $reports->lastPage(),
                    'per_page' => $reports->perPage(),
                    'total_records' => $reports->total(),
                ],
                'summary' => [
                    'total_reports_submitted' => $reports->total(),
                    'total_project_hours_in_page' => round($totalHoursLogged, 2),
                    'active_contributors' => $activeContributors,
                    'unique_projects_count' => $uniqueProjects,
                ],
                'filters_applied' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'employee_id' => $request->employee_id,
                    'project_id' => $request->project_id,
                ],
            ],
        ], 200);
    }

    /**
     * 17. Team Leave Utilization Report
     * History, balances, and breakdown of leaves taken by subordinates.
     */
    public function leaveReport(Request $request)
    {
        $supervisor = $this->authorizeSupervisor($request);
        if (!$supervisor) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Supervisor access required.',
            ], 403);
        }

        $request->validate([
            'year' => 'nullable|integer|min:2020|max:2050',
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
            'employee_id' => 'nullable|integer',
            'status' => 'nullable|string|in:ALL,APPROVED,REJECTED,PENDING,SENT,DRAFT',
            'leave_type_id' => 'nullable|integer',
            'export' => 'nullable|string|in:excel,pdf',
        ]);

        if ($request->input('export') === 'excel') {
            return $this->exportLeaveReportExcel($request);
        }
        if ($request->input('export') === 'pdf') {
            return $this->exportLeaveReportPdf($request);
        }

        $year = (int) $request->input('year', Carbon::now()->year);
        $teamMembers = $supervisor->teamQuery()->with(['designation', 'agency'])->get();
        $teamMemberIds = $teamMembers->pluck('employee_id')->toArray();

        if ($request->filled('employee_id')) {
            $reqEmpId = (int) $request->input('employee_id');
            if (!in_array($reqEmpId, $teamMemberIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee does not belong to your team.',
                ], 404);
            }
            $targetEmpIds = [$reqEmpId];
            $targetMembers = $teamMembers->where('employee_id', $reqEmpId);
        } else {
            $targetEmpIds = $teamMemberIds;
            $targetMembers = $teamMembers;
        }

        $query = Leave::with(['employee.designation', 'leaveType', 'holiday'])
            ->whereIn('employee_id', $targetEmpIds);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $sDate = $request->input('start_date');
            $eDate = $request->input('end_date');
            $query->where(function ($q) use ($sDate, $eDate) {
                $q->whereBetween('from_date', [$sDate, $eDate])
                  ->orWhereBetween('to_date', [$sDate, $eDate]);
            });
        } else {
            $query->where(function ($q) use ($year) {
                $q->whereYear('from_date', $year)->orWhereYear('to_date', $year);
            });
        }

        if ($request->filled('status') && strtoupper($request->status) !== 'ALL') {
            $st = strtoupper($request->status);
            if ($st === 'PENDING') {
                $query->whereIn('status', ['SENT', 'PENDING', 'DRAFT']);
            } else {
                $query->where('status', $st);
            }
        }

        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', (int) $request->input('leave_type_id'));
        }

        $leaves = $query->orderBy('from_date', 'desc')->get();

        $formattedLeaves = $leaves->map(fn ($l) => $this->formatTeamLeave($l));

        // Member-wise balances
        $balancesByMember = [];
        foreach ($targetMembers as $m) {
            $balancesByMember[] = [
                'employee_id' => $m->employee_id,
                'employee_name' => $m->employee_name,
                'designation' => $m->designation?->name ?? 'Staff Member',
                'balances' => $this->calculateEmployeeLeaveBalances($m, $year),
            ];
        }

        $approvedCount = $leaves->where('status', 'APPROVED')->count();
        $approvedDays = $leaves->where('status', 'APPROVED')->sum(function ($l) {
            return $l->duration ?: (Carbon::parse($l->from_date)->diffInDays(Carbon::parse($l->to_date)) + 1);
        });

        return response()->json([
            'success' => true,
            'data' => [
                'year' => $year,
                'summary' => [
                    'total_applications' => $leaves->count(),
                    'approved_count' => $approvedCount,
                    'approved_total_days' => $approvedDays,
                    'pending_count' => $leaves->whereIn('status', ['SENT', 'PENDING', 'DRAFT'])->count(),
                    'rejected_count' => $leaves->where('status', 'REJECTED')->count(),
                ],
                'leaves' => $formattedLeaves,
                'member_balances' => $balancesByMember,
            ],
        ], 200);
    }

    /**
     * Export Team Leave Report to Excel.
     */
    public function exportLeaveReportExcel(Request $request)
    {
        $supervisor = $this->authorizeSupervisor($request);
        if (!$supervisor) {
            return response()->json(['success' => false, 'message' => 'Unauthorized: Supervisor access required.'], 403);
        }

        $data = $this->buildLeaveReportData($supervisor, $request);
        $fileName = 'Team_Leave_Report_' . $supervisor->employee_id . '_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new SupervisorLeaveReportExport($data), $fileName);
    }

    /**
     * Export Team Leave Report to PDF.
     */
    public function exportLeaveReportPdf(Request $request)
    {
        $supervisor = $this->authorizeSupervisor($request);
        if (!$supervisor) {
            return response()->json(['success' => false, 'message' => 'Unauthorized: Supervisor access required.'], 403);
        }

        $data = $this->buildLeaveReportData($supervisor, $request);
        $fileName = 'Team_Leave_Report_' . $supervisor->employee_id . '_' . now()->format('Ymd_His') . '.pdf';
        $pdf = Pdf::loadView('supervisor.reports.leave_pdf', $data)
            ->setPaper('a4', 'landscape');
        return $pdf->download($fileName);
    }

    /**
     * Helper to aggregate leave report data for PDF and Excel export.
     */
    public function buildLeaveReportData($supervisor, Request $request): array
    {
        $year = (int) $request->input('year', Carbon::now()->year);
        $teamMembers = $supervisor->teamQuery()->with(['designation', 'agency'])->get();
        $teamMemberIds = $teamMembers->pluck('employee_id')->toArray();

        if ($request->filled('employee_id')) {
            $reqEmpId = (int) $request->input('employee_id');
            if (in_array($reqEmpId, $teamMemberIds)) {
                $targetEmpIds = [$reqEmpId];
                $targetMembers = $teamMembers->where('employee_id', $reqEmpId);
            } else {
                $targetEmpIds = $teamMemberIds;
                $targetMembers = $teamMembers;
            }
        } else {
            $targetEmpIds = $teamMemberIds;
            $targetMembers = $teamMembers;
        }

        $query = Leave::with(['employee.designation', 'employee.agency', 'leaveType', 'holiday'])
            ->whereIn('employee_id', $targetEmpIds);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $sDate = $request->input('start_date');
            $eDate = $request->input('end_date');
            $query->where(function ($q) use ($sDate, $eDate) {
                $q->whereBetween('from_date', [$sDate, $eDate])
                  ->orWhereBetween('to_date', [$sDate, $eDate]);
            });
            $periodText = Carbon::parse($sDate)->format('d M, Y') . ' - ' . Carbon::parse($eDate)->format('d M, Y');
        } else {
            $query->where(function ($q) use ($year) {
                $q->whereYear('from_date', $year)->orWhereYear('to_date', $year);
            });
            $periodText = "Calendar Year " . $year;
        }

        if ($request->filled('status') && strtoupper($request->status) !== 'ALL') {
            $st = strtoupper($request->status);
            if ($st === 'PENDING') {
                $query->whereIn('status', ['SENT', 'PENDING', 'DRAFT']);
            } else {
                $query->where('status', $st);
            }
        }

        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', (int) $request->input('leave_type_id'));
        }

        $leaves = $query->orderBy('from_date', 'desc')->get();

        $formattedLeaves = [];
        foreach ($leaves as $l) {
            $emp = $l->employee;
            $duration = $l->duration ?: (Carbon::parse($l->from_date)->diffInDays(Carbon::parse($l->to_date)) + 1);
            $formattedLeaves[] = [
                'leave_id' => $l->id,
                'employee_id' => $emp?->employee_id,
                'employee_name' => $emp?->employee_name,
                'designation' => $emp?->designation?->name ?? 'Staff Member',
                'agency' => $emp?->agency?->agency_name ?? 'Direct',
                'leave_type' => $l->leaveType?->leave_name ?? 'Leave',
                'from_date' => Carbon::parse($l->from_date)->format('d M, Y'),
                'to_date' => Carbon::parse($l->to_date)->format('d M, Y'),
                'duration' => $duration . ' Day' . ($duration > 1 ? 's' : ''),
                'status' => strtoupper($l->status),
                'reason' => $l->reason ?: 'No reason provided',
            ];
        }

        $balancesByMember = [];
        foreach ($targetMembers as $m) {
            $balancesByMember[] = [
                'employee_id' => $m->employee_id,
                'employee_name' => $m->employee_name,
                'designation' => $m->designation?->name ?? 'Staff Member',
                'balances' => $this->calculateEmployeeLeaveBalances($m, $year),
            ];
        }

        $approvedCount = $leaves->where('status', 'APPROVED')->count();
        $approvedDays = $leaves->where('status', 'APPROVED')->sum(function ($l) {
            return $l->duration ?: (Carbon::parse($l->from_date)->diffInDays(Carbon::parse($l->to_date)) + 1);
        });

        $summary = [
            'total_applications' => $leaves->count(),
            'approved_count' => $approvedCount,
            'approved_total_days' => $approvedDays,
            'pending_count' => $leaves->whereIn('status', ['SENT', 'PENDING', 'DRAFT'])->count(),
            'rejected_count' => $leaves->where('status', 'REJECTED')->count(),
        ];

        return [
            'supervisor' => $supervisor,
            'year' => $year,
            'periodText' => $periodText,
            'leaves' => $formattedLeaves,
            'member_balances' => $balancesByMember,
            'summary' => $summary,
            'companyDetails' => Company::first(),
        ];
    }

    /**
     * Helper to safely parse string hours format (e.g., "10:0", "8:15", "8.5") to float decimal.
     */
    protected function parseHoursStringToDecimal($val): float
    {
        if (empty($val)) {
            return 0.0;
        }
        if (is_numeric($val)) {
            return (float) $val;
        }
        if (str_contains((string) $val, ':')) {
            $parts = explode(':', (string) $val);
            $h = (int) ($parts[0] ?? 0);
            $m = (int) ($parts[1] ?? 0);
            return round($h + ($m / 60), 2);
        }
        return (float) $val;
    }
}

