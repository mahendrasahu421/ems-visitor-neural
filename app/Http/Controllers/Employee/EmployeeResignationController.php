<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeResignation;
use App\Services\ResignationNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeResignationController extends Controller
{
    protected ResignationNotificationService $notificationService;

    public function __construct(ResignationNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Resignation Overview & History for the authenticated employee.
     */
    public function index()
    {
        $employee = Auth::guard('employee')->user();
        if (!$employee) {
            return redirect()->route('employee.login');
        }

        $activeResignation = EmployeeResignation::where('employee_id', $employee->employee_id)
            ->whereIn('status', ['pending', 'in_review', 'approved'])
            ->latest('id')
            ->first();

        $history = EmployeeResignation::where('employee_id', $employee->employee_id)
            ->orderByDesc('id')
            ->paginate(10);

        return view('employee.resignation.index', [
            'title'             => 'Resignation & Exit Management',
            'employee'          => $employee,
            'activeResignation' => $activeResignation,
            'history'           => $history,
        ]);
    }

    /**
     * Show resignation application form.
     */
    public function create()
    {
        $employee = Auth::guard('employee')->user();
        if (!$employee) {
            return redirect()->route('employee.login');
        }

        $existing = EmployeeResignation::where('employee_id', $employee->employee_id)
            ->whereIn('status', ['pending', 'in_review', 'approved'])
            ->first();

        if ($existing) {
            return redirect()->route('employee.resignation.index')
                ->with('warning', 'You already have an active resignation request (' . $existing->resignation_code . ').');
        }

        $defaultNoticeDays = 30;
        $suggestedLwd = Carbon::today()->addDays($defaultNoticeDays)->format('Y-m-d');

        return view('employee.resignation.create', [
            'title'             => 'Apply for Resignation',
            'employee'          => $employee,
            'supervisor'        => $employee->supervisorEmployee,
            'defaultNoticeDays' => $defaultNoticeDays,
            'suggestedLwd'      => $suggestedLwd,
        ]);
    }

    /**
     * Store new resignation request.
     */
    public function store(Request $request)
    {
        $employee = Auth::guard('employee')->user();
        if (!$employee) {
            return redirect()->route('employee.login');
        }

        $existing = EmployeeResignation::where('employee_id', $employee->employee_id)
            ->whereIn('status', ['pending', 'in_review', 'approved'])
            ->first();

        if ($existing) {
            return redirect()->route('employee.resignation.index')
                ->with('error', 'You already have an active resignation request.');
        }

        $request->validate([
            'reason'                     => 'required|string|max:150',
            'requested_last_working_day' => 'required|date|after:today',
            'notice_period_days'         => 'required|integer|min:0|max:180',
            'reason_details'             => 'required|string|min:15|max:5000',
            'cc_emails'                  => 'nullable|array',
            'cc_emails.*'                => 'nullable|email|max:150',
            'resignation_document'       => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:5120',
        ]);

        $docPath = null;
        if ($request->hasFile('resignation_document')) {
            $file = $request->file('resignation_document');
            $fileName = 'resignation_' . $employee->employee_id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/resignations'), $fileName);
            $docPath = 'uploads/resignations/' . $fileName;
        }

        $cleanCcs = array_values(array_unique(array_filter(
            (array) $request->input('cc_emails', []),
            fn($e) => !empty($e) && filter_var($e, FILTER_VALIDATE_EMAIL)
        )));

        $resignation = EmployeeResignation::create([
            'resignation_code'           => EmployeeResignation::generateCode(),
            'employee_id'                => $employee->employee_id,
            'initiated_by'               => 'employee',
            'initiator_id'               => $employee->employee_id,
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

        // Send Email Notification with CCs
        $this->notificationService->sendSubmissionNotification($resignation);

        return redirect()->route('employee.resignation.index')
            ->with('success', 'Your resignation request (' . $resignation->resignation_code . ') has been submitted successfully. Notification has been emailed to your supervisor and HR.');
    }

    /**
     * Show single resignation details.
     */
    public function show($id)
    {
        $employee = Auth::guard('employee')->user();
        if (!$employee) {
            return redirect()->route('employee.login');
        }

        $resignation = EmployeeResignation::with(['employee.designation', 'employee.agency', 'supervisor'])
            ->where('employee_id', $employee->employee_id)
            ->findOrFail($id);

        return view('employee.resignation.show', [
            'title'       => 'Resignation Details - ' . $resignation->resignation_code,
            'resignation' => $resignation,
            'employee'    => $employee,
        ]);
    }

    /**
     * Withdraw pending resignation.
     */
    public function withdraw(Request $request, $id)
    {
        $employee = Auth::guard('employee')->user();
        if (!$employee) {
            return redirect()->route('employee.login');
        }

        $resignation = EmployeeResignation::where('employee_id', $employee->employee_id)
            ->where('status', 'pending')
            ->findOrFail($id);

        $resignation->status = 'withdrawn';
        $resignation->action_remarks = $request->input('withdraw_reason', 'Withdrawn voluntarily by employee');
        $resignation->action_at = now();
        $resignation->action_by_type = 'employee';
        $resignation->action_by_id = $employee->employee_id;
        $resignation->action_by_name = $employee->employee_name;
        $resignation->save();

        return redirect()->route('employee.resignation.index')
            ->with('success', 'Your resignation request has been withdrawn successfully.');
    }
}
