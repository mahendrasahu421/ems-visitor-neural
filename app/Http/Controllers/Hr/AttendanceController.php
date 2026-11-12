<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Holiday;
use App\Models\Designation;
use App\Models\Leave;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Mail\LeaveStatusMail;
use Illuminate\Support\Facades\Mail;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] = 'Leave Requests';
        $today = Carbon::today();

        $data['totalRequests'] = Leave::count();
        $data['pendingCount'] = Leave::whereIn('status', ['Sent', 'SENT', 'Pending', 'pending'])->count();
        $data['approvedCount'] = Leave::whereIn('status', ['Approved', 'APPROVED', 'approved'])->count();
        $data['rejectedCount'] = Leave::whereIn('status', ['Rejected', 'REJECTED', 'rejected'])->count();
        $data['todayRequests'] = Leave::whereDate('created_at', $today)->count();
        $data['designation'] = Designation::where('status', 'Active')->orderBy('name')->get();

        return view('home.attendance.index', $data);
    }

    public function list(Request $request)
    {
        try {
            $designationId = $request->designation_id;
            $statusFilter = $request->status_filter;

            // Base query
            $query = Leave::with(['leaveType', 'employee.designation'])->latest('id');

            // Filter: Designation
            if (!empty($designationId)) {
                $query->whereHas('employee', function ($q) use ($designationId) {
                    $q->where('designation_id', $designationId);
                });
            }

            // Filter: Status
            if (!empty($statusFilter)) {
                if (strtolower($statusFilter) === 'pending' || strtolower($statusFilter) === 'sent') {
                    $query->whereIn('status', ['Sent', 'SENT', 'Pending', 'pending']);
                } else {
                    $query->where('status', 'LIKE', $statusFilter);
                }
            }

            // Search filter
            $search = $request->input('search.value');
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('reason', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhereHas('employee', function ($emp) use ($search) {
                            $emp->where('employee_name', 'LIKE', "%{$search}%")
                                ->orWhere('employee_email', 'LIKE', "%{$search}%");
                        })
                        ->orWhereHas('leaveType', function ($type) use ($search) {
                            $type->where('leave_name', 'LIKE', "%{$search}%");
                        });
                });
            }

            $recordsFiltered = $query->count();

            // Pagination
            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $leaves = $query->skip($start)->take($length)->get();

            $data = [];

            foreach ($leaves as $leave) {
                $from = $leave->from_date ? Carbon::parse($leave->from_date) : null;
                $to = $leave->to_date ? Carbon::parse($leave->to_date) : null;
                $days = ($from && $to) ? ($from->diffInDays($to) + 1) : 1;

                $fromFormatted = $from ? $from->format('d M, Y') : '-';
                $toFormatted = $to ? $to->format('d M, Y') : '-';

                // Status badge
                $statusLower = strtolower($leave->status);
                $isPending = in_array($statusLower, ['sent', 'pending']);
                $isApproved = ($statusLower === 'approved');
                $isRejected = ($statusLower === 'rejected');

                if ($isApproved) {
                    $statusBadge = '<span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 fs-11"><i class="ti ti-circle-check fs-12 me-1"></i>Approved</span>';
                } elseif ($isRejected) {
                    $statusBadge = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 fs-11"><i class="ti ti-circle-x fs-12 me-1"></i>Rejected</span>';
                } else {
                    $statusBadge = '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 fs-11"><i class="ti ti-clock fs-12 me-1"></i>Pending</span>';
                }

                // Action buttons
                if ($isPending) {
                    $actionButtons = '
                    <div class="d-flex align-items-center justify-content-end gap-1">
                        <button type="button" class="btn btn-sm btn-success px-2.5 py-1 openModal fw-semibold d-inline-flex align-items-center" data-id="' . $leave->id . '" data-status="Approved" title="Approve Leave">
                            <i class="ti ti-check me-1"></i>Accept
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger px-2.5 py-1 openModal fw-semibold d-inline-flex align-items-center" data-id="' . $leave->id . '" data-status="Rejected" title="Reject Leave">
                            <i class="ti ti-x me-1"></i>Reject
                        </button>
                    </div>';
                } else {
                    $actionButtons = '<div class="text-end pe-2">' . $statusBadge . '</div>';
                }

                $empName = $leave->employee->employee_name ?? 'Unknown Staff';
                $empEmail = $leave->employee->employee_email ?? '';
                $empDesignation = $leave->employee->designation->name ?? '';

                $data[] = [
                    'id' => $leave->id,
                    'employee' => $empName,
                    'employee_email' => $empEmail,
                    'designation' => $empDesignation,
                    'leave_type' => $leave->leaveType->leave_name ?? 'General Leave',
                    'days' => $days,
                    'from_date' => $fromFormatted,
                    'to_date' => $toFormatted,
                    'reason' => $leave->reason ?: 'No reason specified',
                    'status' => $statusBadge,
                    'raw_status' => $leave->status,
                    'action' => $actionButtons,
                    'applied_on' => $leave->created_at ? Carbon::parse($leave->created_at)->format('d M, Y') : '-'
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => Leave::count(),
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request)
    {
        try {
            $request->validate([
                'leave_id' => 'required|integer',
                'new_status' => 'required|in:Approved,Rejected',
                'rejection_reason' => 'nullable|string'
            ]);

            $leave = Leave::with('employee')->find($request->leave_id);

            if (!$leave) {
                return response()->json(['error' => 'Leave not found'], 404);
            }

            $employee = $leave->employee;

            $actorName = auth()->user()->name ?? 'HR / Admin';
            $remarks = $request->rejection_reason ?? ($request->new_status === 'Approved' ? 'Approved by HR/Admin' : 'Rejected by HR/Admin');

            // Update Status in Database
            $leave->status = $request->new_status;
            if ($request->filled('rejection_reason')) {
                $leave->supervisor_remarks = $request->rejection_reason;
            }
            $leave->save();

            // Notify Employee via Email
            \App\Services\LeaveNotificationService::notifyLeaveStatusChanged($leave, $request->new_status, $actorName, $remarks);

            return response()->json([
                'success' => true,
                'message' => 'Leave ' . strtolower($request->new_status) . ' successfully.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }








    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function holidayList()
    {
        $data['title'] = 'Holiday List';

        $holidays = Holiday::where('status', 1)
            ->orderBy('holiday_date', 'asc')
            ->get();

        $events = [];
        foreach ($holidays as $h) {
            $isCommon = str_contains(strtolower($h->holiday_remark ?? ''), 'common');
            $events[] = [
                'title' => ($isCommon ? '★ ' : '') . $h->holiday_name,
                'start' => $h->holiday_date,
                'className' => $isCommon ? 'holiday-event common-holiday' : 'holiday-event restricted-holiday',
                'display' => 'block'
            ];
        }

        $data['events'] = $events;

        return view('employee.holiday.index', $data);
    }
}
