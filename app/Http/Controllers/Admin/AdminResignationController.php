<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Employee;
use App\Models\EmployeeResignation;
use App\Services\ResignationNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminResignationController extends Controller
{
    protected ResignationNotificationService $notificationService;

    public function __construct(ResignationNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Resignation & Exit Reporting Dashboard.
     */
    public function index(Request $request)
    {
        $query = EmployeeResignation::with(['employee.designation', 'employee.agency', 'supervisor']);

        // Filter: Status
        $selectedStatus = $request->input('status', 'all');
        if ($selectedStatus && $selectedStatus !== 'all') {
            $query->where('status', $selectedStatus);
        }

        // Filter: Initiated By
        if ($request->filled('initiated_by') && $request->initiated_by !== 'all') {
            $query->where('initiated_by', $request->initiated_by);
        }

        // Filter: Supervisor
        if ($request->filled('supervisor_id') && $request->supervisor_id !== 'all') {
            $query->where('supervisor_id', $request->supervisor_id);
        }

        // Filter: Employee
        if ($request->filled('employee_id') && $request->employee_id !== 'all') {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter: Agency
        if ($request->filled('agency_id') && $request->agency_id !== 'all') {
            $query->whereHas('employee', function ($q) use ($request) {
                if ($request->agency_id === 'unassigned') {
                    $q->whereNull('agency_id');
                } else {
                    $q->where('agency_id', $request->agency_id);
                }
            });
        }

        // Filter: Date Range
        if ($request->filled('from_date')) {
            $query->whereDate('resignation_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('resignation_date', '<=', $request->to_date);
        }

        // Filter: Search
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('resignation_code', 'like', "%{$s}%")
                    ->orWhere('reason', 'like', "%{$s}%")
                    ->orWhereHas('employee', function ($eq) use ($s) {
                        $eq->where('employee_name', 'like', "%{$s}%")
                            ->orWhere('employee_email', 'like', "%{$s}%")
                            ->orWhere('employee_mobile', 'like', "%{$s}%");
                    });
            });
        }

        // KPI Counts (Global base)
        $kpiBase = EmployeeResignation::query();
        $totalCount     = (clone $kpiBase)->count();
        $pendingCount   = (clone $kpiBase)->whereIn('status', ['pending', 'in_review'])->count();
        $approvedCount  = (clone $kpiBase)->where('status', 'approved')->count();
        $rejectedCount  = (clone $kpiBase)->where('status', 'rejected')->count();
        $completedCount = (clone $kpiBase)->where('status', 'completed')->count();

        $resignations = $query->orderByDesc('id')->paginate(15)->withQueryString();

        // Masters for filters
        $supervisors = Employee::where('is_supervisor', 1)->orWhere('is_supervisor', '1')->orderBy('employee_name')->get(['employee_id', 'employee_name']);
        $employees   = Employee::whereNull('deleted_at')->orderBy('employee_name')->get(['employee_id', 'employee_name', 'status']);
        $agencies    = Agency::where('status', 'Active')->orderBy('agency_name')->get();

        return view('admin.resignation.index', [
            'title'           => 'Employee Resignation & Exit Reporting',
            'resignations'    => $resignations,
            'totalCount'      => $totalCount,
            'pendingCount'    => $pendingCount,
            'approvedCount'   => $approvedCount,
            'rejectedCount'   => $rejectedCount,
            'completedCount'  => $completedCount,
            'selectedStatus'  => $selectedStatus,
            'supervisors'     => $supervisors,
            'employees'       => $employees,
            'agencies'        => $agencies,
        ]);
    }

    /**
     * Show form for Supervisor/Admin to initiate separation of an employee.
     */
    public function create()
    {
        $activeEmployees = Employee::active()
            ->with(['designation', 'agency', 'supervisorEmployee'])
            ->orderBy('employee_name')
            ->get();

        $defaultNoticeDays = 30;
        $suggestedLwd = Carbon::today()->addDays($defaultNoticeDays)->format('Y-m-d');

        return view('admin.resignation.create', [
            'title'             => 'Initiate Employee Separation / Exit',
            'employees'         => $activeEmployees,
            'defaultNoticeDays' => $defaultNoticeDays,
            'suggestedLwd'      => $suggestedLwd,
        ]);
    }

    /**
     * Store separation initiated by Admin or Supervisor.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id'                => 'required|exists:employees,employee_id',
            'reason'                     => 'required|string|max:150',
            'requested_last_working_day' => 'required|date',
            'notice_period_days'         => 'required|integer|min:0|max:180',
            'reason_details'             => 'required|string|min:10|max:5000',
            'cc_emails'                  => 'nullable|array',
            'cc_emails.*'                => 'nullable|email|max:150',
            'resignation_document'       => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:5120',
        ]);

        $employee = Employee::where('employee_id', $request->employee_id)->firstOrFail();

        $existing = EmployeeResignation::where('employee_id', $employee->employee_id)
            ->whereIn('status', ['pending', 'in_review', 'approved'])
            ->first();

        if ($existing) {
            return redirect()->back()
                ->with('error', 'This employee already has an active exit request (' . $existing->resignation_code . ').')
                ->withInput();
        }

        $docPath = null;
        if ($request->hasFile('resignation_document')) {
            $file = $request->file('resignation_document');
            $fileName = 'exit_' . $employee->employee_id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/resignations'), $fileName);
            $docPath = 'uploads/resignations/' . $fileName;
        }

        $cleanCcs = array_values(array_unique(array_filter(
            (array) $request->input('cc_emails', []),
            fn($e) => !empty($e) && filter_var($e, FILTER_VALIDATE_EMAIL)
        )));

        $currentUser = Auth::user();
        $isSupervisor = false;
        $initiatorType = 'admin';
        $initiatorId = $currentUser?->id;

        $resignation = EmployeeResignation::create([
            'resignation_code'           => EmployeeResignation::generateCode(),
            'employee_id'                => $employee->employee_id,
            'initiated_by'               => $initiatorType,
            'initiator_id'               => $initiatorId,
            'supervisor_id'              => $employee->supervisor_id,
            'resignation_date'           => Carbon::today(),
            'requested_last_working_day' => $request->requested_last_working_day,
            'notice_period_days'         => $request->notice_period_days,
            'reason'                     => $request->reason,
            'reason_details'             => $request->reason_details,
            'cc_emails'                  => $cleanCcs,
            'status'                     => 'pending',
            'decision_token'             => EmployeeResignation::generateDecisionToken(),
            'document_path'              => $docPath,
        ]);

        // Send Email Notification to Employee, Supervisor, and CCs
        $this->notificationService->sendSubmissionNotification($resignation);

        return redirect()->route('admin.resignation.index')
            ->with('success', 'Separation notice (' . $resignation->resignation_code . ') initiated successfully. Notification sent via email.');
    }

    /**
     * Show single resignation record.
     */
    public function show($id)
    {
        $resignation = EmployeeResignation::with(['employee.designation', 'employee.agency', 'supervisor'])
            ->findOrFail($id);

        return view('admin.resignation.show', [
            'title'       => 'Resignation Record - ' . $resignation->resignation_code,
            'resignation' => $resignation,
        ]);
    }

    /**
     * Review & Respond (Accept / Reject) on Resignation.
     */
    public function respond(Request $request, $id)
    {
        $resignation = EmployeeResignation::with('employee')->findOrFail($id);

        $request->validate([
            'decision'                   => 'required|in:approved,rejected',
            'approved_last_working_day'  => 'nullable|required_if:decision,approved|date',
            'action_remarks'             => 'required|string|min:5|max:2000',
        ]);

        $reviewer = Auth::user();
        $reviewerName = $reviewer?->name ?: 'Administrator';

        $resignation->status = $request->decision;
        if ($request->decision === 'approved') {
            $resignation->approved_last_working_day = $request->approved_last_working_day ?: $resignation->requested_last_working_day;
        }
        $resignation->action_by_type = 'admin';
        $resignation->action_by_id = $reviewer?->id;
        $resignation->action_by_name = $reviewerName;
        $resignation->action_at = now();
        $resignation->action_remarks = $request->action_remarks;
        $resignation->save();

        // Dispatch Email Notification
        $this->notificationService->sendDecisionNotification($resignation);

        $word = $request->decision === 'approved' ? 'Accepted & Approved' : 'Rejected';

        return redirect()->back()
            ->with('success', "Resignation {$resignation->resignation_code} has been successfully {$word}. Decision email sent to employee and CC recipients.");
    }

    /**
     * Direct 1-Click Action via Secure Email Token.
     */
    public function emailAction(Request $request, string $token, string $decision = 'view')
    {
        $resignation = EmployeeResignation::with(['employee.designation', 'employee.agency', 'supervisor'])
            ->where('decision_token', $token)
            ->first();

        if (!$resignation) {
            return view('admin.resignation.email_action', [
                'title'       => 'Invalid or Expired Link',
                'error'       => 'The resignation link is invalid or has expired.',
                'resignation' => null,
            ]);
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'decision'                  => 'required|in:approved,rejected',
                'approved_last_working_day' => 'nullable|required_if:decision,approved|date',
                'action_remarks'            => 'required|string|min:5|max:2000',
                'reviewer_name'             => 'required|string|max:150',
            ]);

            if (!in_array($resignation->status, ['pending', 'in_review'], true)) {
                return redirect()->back()->with('warning', 'This resignation request has already been marked as ' . strtoupper($resignation->status) . '.');
            }

            $resignation->status = $request->decision;
            if ($request->decision === 'approved') {
                $resignation->approved_last_working_day = $request->approved_last_working_day ?: $resignation->requested_last_working_day;
            }
            $resignation->action_by_type = 'email_reviewer';
            $resignation->action_by_name = $request->reviewer_name;
            $resignation->action_at = now();
            $resignation->action_remarks = $request->action_remarks;
            $resignation->save();

            // Send Decision Mail
            $this->notificationService->sendDecisionNotification($resignation);

            return view('admin.resignation.email_action', [
                'title'       => 'Response Recorded Successfully',
                'success'     => 'Thank you. Your response (' . strtoupper($request->decision) . ') for ' . $resignation->resignation_code . ' has been recorded and notification has been dispatched.',
                'resignation' => $resignation,
                'done'        => true,
            ]);
        }

        return view('admin.resignation.email_action', [
            'title'            => 'Review Resignation - ' . $resignation->resignation_code,
            'resignation'      => $resignation,
            'selectedDecision' => in_array($decision, ['accept', 'approved'], true) ? 'approved' : (in_array($decision, ['reject', 'rejected'], true) ? 'rejected' : 'approved'),
        ]);
    }

    /**
     * Mark exit completed and deactivate employee.
     */
    public function completeExit(Request $request, $id)
    {
        $resignation = EmployeeResignation::with('employee')->findOrFail($id);

        if ($resignation->status !== 'approved') {
            return redirect()->back()->with('error', 'Only approved resignations can be finalized for employee relieving.');
        }

        $resignation->status = 'completed';
        $resignation->feedback_rating = $request->input('feedback_rating');
        $resignation->feedback_notes = $request->input('feedback_notes');
        $resignation->save();

        // Formally Deactivate Employee across the whole system
        if ($resignation->employee) {
            $resignation->employee->deactivate();
        }

        return redirect()->back()
            ->with('success', "Employee {$resignation->employee?->employee_name} has been relieved successfully. Their account status is now set to Inactive, API tokens revoked, and portal access disabled.");
    }

    /**
     * Export resignation report to CSV.
     */
    public function export(Request $request)
    {
        $fileName = 'Resignations_Report_' . date('Ymd_His') . '.csv';

        $query = EmployeeResignation::with(['employee.designation', 'employee.agency', 'supervisor'])->latest('id');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $records = $query->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Resignation Code',
                'Employee ID',
                'Employee Name',
                'Employee Email',
                'Designation',
                'Agency',
                'Supervisor',
                'Initiated By',
                'Submission Date',
                'Requested LWD',
                'Approved LWD',
                'Notice Period (Days)',
                'Reason',
                'Status',
                'Reviewed By',
                'Reviewed At',
                'Remarks',
            ]);

            foreach ($records as $r) {
                fputcsv($file, [
                    $r->resignation_code,
                    $r->employee?->employee_id ?? '',
                    $r->employee?->employee_name ?? '',
                    $r->employee?->employee_email ?? '',
                    $r->employee?->designation?->name ?? 'Staff',
                    $r->employee?->agency?->agency_name ?? 'Direct',
                    $r->supervisor?->employee_name ?? 'N/A',
                    ucfirst($r->initiated_by),
                    $r->resignation_date?->format('Y-m-d') ?? '',
                    $r->requested_last_working_day?->format('Y-m-d') ?? '',
                    $r->approved_last_working_day?->format('Y-m-d') ?? '',
                    $r->notice_period_days,
                    $r->reason,
                    strtoupper($r->status),
                    $r->action_by_name ?? '',
                    $r->action_at?->format('Y-m-d H:i') ?? '',
                    $r->action_remarks ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
