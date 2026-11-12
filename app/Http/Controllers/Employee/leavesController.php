<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveType;
use App\Models\Leave;
use App\Models\Employee;
use App\Models\User;
use App\Models\EmailTemplate;
use App\Models\LeaveMapping;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\LeaveAppliedMail;

class leavesController extends Controller
{
    public function index()
    {
        $data['title'] = 'Leaves';
        $data['titleRoute'] = 'Leave Management / Leaves List';
        $data['imageUrl'] = "https://picsum.photos/200/200?random=" . rand(1, 1000);

        $employeeId = auth('employee')->id();
        $employee = Employee::find($employeeId);

        if (!$employee) {
            abort(404, 'Employee not found');
        }

        $designationId = $employee->designation_id;

        // Fetch leave mappings for this designation
        $leaveMappings = LeaveMapping::with('leaveType')
            ->where('designation_id', $designationId)
            ->get();

        $totalAllotted = 0;
        $totalUsed = 0;
        $leaveSummary = [];

        foreach ($leaveMappings as $mapping) {

            $leaveType = $mapping->leaveType;

            // ❌ Not applicable? Skip
            if (!$this->isLeaveApplicable($employee, $leaveType)) {
                continue;
            }

            // Used leaves calculation
            $used = Leave::where('employee_id', $employeeId)
                ->where('leave_type_id', $leaveType->id)
                ->where('status', 'APPROVED')
                ->whereYear('from_date', Carbon::now()->year)
                ->get()
                ->sum(function ($leave) {
                    return Carbon::parse($leave->from_date)
                        ->diffInDays(Carbon::parse($leave->to_date)) + 1;
                });

            $remaining = max($mapping->allow_days - $used, 0);

            $totalAllotted += $mapping->allow_days;
            $totalUsed += $used;

            $leaveSummary[] = [
                'id' => $leaveType->id,
                'name' => $leaveType->leave_name,
                'total_leaves' => $mapping->allow_days,
                'used' => $used,
                'remaining' => $remaining,
                'icon' => $this->getLeaveIcon($leaveType->leave_name),
                'color' => $this->getLeaveColor($leaveType->leave_name),
            ];
        }

        $data['leaveSummary'] = $leaveSummary;
        $data['totalAllotted'] = $totalAllotted;
        $data['totalUsed'] = $totalUsed;
        $data['totalRemaining'] = max($totalAllotted - $totalUsed, 0);

        return view('employee.leaves.index', $data);
    }


    private function isLeaveApplicable($employee, $leaveType)
    {
        // ✔ Gender-based applicability
        if ($leaveType->applicable_for !== 'All') {
            if (strtoupper($leaveType->applicable_for) !== strtoupper($employee->employee_gender)) {
                return false;
            }
        }

        // ✔ New employee restriction: joining date < 2 months
        $joinDate = Carbon::parse($employee->joining_date);
        $monthsWorked = $joinDate->diffInMonths(Carbon::now());

        if ($monthsWorked < 2) {
            return false; // Leave is not allowed until the employee completes 2 months.
        }

        return true;
    }



    // ✅ Helper functions same rahenge
    private function getLeaveIcon($name)
    {
        return match (strtolower($name)) {
            'annual leave' => 'ti ti-calendar-event',
            'medical leave' => 'ti ti-vaccine',
            'casual leave' => 'ti ti-hexagon-letter-c',
            default => 'ti ti-hexagonal-prism-plus',
        };
    }

    private function getLeaveColor($name)
    {
        return match (strtolower($name)) {
            'annual leave' => 'bg-black-le',
            'medical leave' => 'bg-blue-le',
            'casual leave' => 'bg-purple-le',
            default => 'bg-pink-le',
        };
    }
    public function create()
    {
        $data['title'] = 'Leaves';
        $data['titleRoute'] = 'Leave Management / Leaves Apply';

        $employeeId = auth('employee')->id();
        $employee = Employee::find($employeeId);

        if (!$employee || !$employee->isActive()) {
            abort(403, 'Your employee account is inactive. You cannot apply for leaves.');
        }

        $employeeGender = strtoupper($employee->gender);

        // 🔹 Current date
        $today = Carbon::now();

        // 🔹 Months since joining
        $monthsWorked = Carbon::parse($employee->joining_date)->diffInMonths($today);

        // 👉 Filter leave types based on gender AND minimum service (2 months)
        $data['leaveTypes'] = LeaveType::where(function ($q) use ($employeeGender) {
            $q->where('applicable_for', 'ALL')
                ->orWhere('applicable_for', $employeeGender);
        })
            ->when($monthsWorked < 2, function ($q) {
                // 🔹 If employee is new (<2 months), exclude all leave types except "Leave Without Pay"
                $q->where('leave_name', 'Leave Without Pay');
            })
            ->get();

        $data['imageUrl'] = "https://picsum.photos/200/200?random=" . rand(1, 1000);

        // echo "<pre>"; print_r($data); echo exit; 
        return view('employee.leaves.create', $data);
        
    }


    public function store(Request $request)
    {
        try {

            // Validation
            $request->validate([
                'leave_type_id' => 'required|exists:leave_types,id',
                'from_date' => 'required|date',
                'to_date' => 'required|date|after_or_equal:from_date',
                'reason' => 'required|string|max:255',
                'status' => 'required',
            ]);

            $employeeId = auth('employee')->id();
            $employee = Employee::find($employeeId);

            if (!$employee || !$employee->isActive()) {
                return back()->with('error', 'Your employee account is inactive. You cannot apply for leave.')->withInput();
            }

            // ⭐ Check if already applied today
            $alreadyAppliedToday = Leave::where('employee_id', $employeeId)
                ->whereDate('created_at', Carbon::today())
                ->exists();

            if ($alreadyAppliedToday) {
                return back()->with('error', 'You have already applied for leave today.')->withInput();
            }

            // Leave Mapping
            $leaveMapping = LeaveMapping::where('designation_id', $employee->designation_id)
                ->where('leave_type_id', $request->leave_type_id)
                ->first();

            if (!$leaveMapping) {
                return back()->with('error', 'You are not allowed to apply for this leave type')->withInput();
            }

            // Day Calculation
            $from = Carbon::parse($request->from_date);
            $to = Carbon::parse($request->to_date);
            $daysRequested = $from->diffInDays($to) + 1;

            if ($daysRequested > $leaveMapping->allow_days) {
                return back()->with('error', 'You are requesting more days than allowed')->withInput();
            }

            // Insert Leave
            $leave = Leave::create([
                'employee_id' => $employeeId,
                'leave_type_id' => $request->leave_type_id,
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'reason' => $request->reason,
                'status' => $request->status
            ]);

            // Email Notification to Supervisor & Admin(s)
            \App\Services\LeaveNotificationService::notifyLeaveApplied($leave);

            return redirect()->route('employee.leaves')
                ->with('success', 'Leave applied successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            \Log::error('Leave Apply Error: ' . $e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
        
    }






    public function list(Request $request)
    {
        try {
            $search = $request->input('search')['value'] ?? null;
            $limit = $request->input('length', 10);
            $start = $request->input('start', 0);

            // ✅ Authenticated employee ID
            $employeeId = auth('employee')->id();

            // ✅ Base query for employee's leave records
            $query = Leave::with('leaveType')
                ->where('employee_id', $employeeId);

            // 🔹 Filter by selected leave type
            $leaveTypeId = $request->input('leave_type'); // from DataTable ajax
            if ($leaveTypeId) {
                $query->where('leave_type_id', $leaveTypeId);
            }

            // ✅ Apply search filter
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('reason', 'like', "%$search%")
                        ->orWhereHas('leaveType', function ($q2) use ($search) {
                            $q2->where('leave_name', 'like', "%$search%");
                        });
                });
            }

            $totalRecord = $query->count();

            // ✅ Fetch paginated data
            $leaves = $query->skip($start)->take($limit)->get();

            $rows = [];
            foreach ($leaves as $index => $leave) {
                $rows[] = [
                    'DT_RowIndex' => $start + $index + 1,
                    'leave_type' => $leave->leaveType->leave_name ?? '--',
                    'from_date' => $leave->from_date ?? '--',
                    'to_date' => $leave->to_date ?? '--',
                    'duration' => $leave->duration ?? '--',
                    'reason' => $leave->reason ?? '--',
                    'status' => match (strtolower($leave->status)) {
                        'approved' => '<span class="badge badge-success d-inline-flex align-items-center badge-xs">
                                        <i class="ti ti-point-filled me-1"></i>Approved
                                   </span>',
                        'sent' => '<span class="badge badge-warning d-inline-flex align-items-center badge-xs">
                                        <i class="ti ti-point-filled me-1"></i>Approval Pending
                                   </span>',
                        'rejected' => '<span class="badge badge-danger d-inline-flex align-items-center badge-xs">
                                        <i class="ti ti-point-filled me-1"></i>Rejected
                                   </span>',
                        default => '<span class="badge badge-secondary d-inline-flex align-items-center badge-xs">
                                        <i class="ti ti-point-filled me-1"></i>--</span>',
                    },
                    'created_at' => $leave->created_at->format('d M Y'),
                    'action' => '<a href="#" class="btn btn-sm btn-primary">View</a>',
                ];
            }

            // ✅ Return JSON for DataTable
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecord,
                'recordsFiltered' => $totalRecord,
                'data' => $rows,
            ]);
        } catch (\Exception $e) {
            // ⚠️ Handle error gracefully
            return response()->json([
                'error' => true,
                'message' => 'Something went wrong while fetching leave records.',
                'exception' => $e->getMessage(),
            ], 500);
        }
    }


    public function getLeaveBalance($leaveTypeId)
    {
        $employeeId = auth('employee')->id();
        $leaveType = LeaveType::find($leaveTypeId);

        if (!$leaveType) {
            return response()->json([
                'allotted' => 0,
                'used' => 0,
                'remaining' => 0,
                'leave_name' => ''
            ]);
        }

        // ⭐ Leave Without Pay Handling
        if ($leaveType->leave_name === "Leave Without Pay") {

            return response()->json([
                'allotted' => "Unlimited",
                'used' => "-",
                'remaining' => "Unlimited",
                'leave_name' => "Leave Without Pay"
            ]);
        }

        // Paid leave mapping
        $mapping = LeaveMapping::where('designation_id', auth('employee')->user()->designation_id)
            ->where('leave_type_id', $leaveTypeId)
            ->first();

        if (!$mapping) {
            return response()->json([
                'allotted' => 0,
                'used' => 0,
                'remaining' => 0,
                'leave_name' => $leaveType->leave_name
            ]);
        }

        // Used leaves
        $used = Leave::where('employee_id', $employeeId)
            ->where('leave_type_id', $leaveTypeId)
            ->where('status', 'APPROVED')
            ->whereYear('from_date', Carbon::now()->year)
            ->get()
            ->sum(function ($leave) {
                return Carbon::parse($leave->from_date)
                    ->diffInDays(Carbon::parse($leave->to_date)) + 1;
            });

        $remaining = max($mapping->allow_days - $used, 0);

        return response()->json([
            'allotted' => $mapping->allow_days,
            'used' => $used,
            'remaining' => $remaining,
            'leave_name' => $leaveType->leave_name
        ]);
    }
}
