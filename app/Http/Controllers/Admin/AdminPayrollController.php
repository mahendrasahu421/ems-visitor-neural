<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\PayrollBatch;
use App\Models\PayrollWorkingDaysSetting;
use App\Models\Payslip;
use App\Models\SalaryStructure;
use App\Services\PayrollService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AdminPayrollController extends Controller
{
    protected PayrollService $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    /**
     * Show Monthly Payroll Process Screen.
     */
    public function processIndex()
    {
        $data['title'] = 'Create Payroll Batch & Monthly Processing';
        $data['currentMonth'] = Carbon::now()->month;
        $data['currentYear'] = Carbon::now()->year;
        
        $data['activeEmployeesCount'] = Employee::active()->count();
        $data['employeesWithStructure'] = SalaryStructure::where('status', 'Active')
            ->whereHas('employee', function ($q) {
                $q->active();
            })
            ->distinct('employee_id')->count('employee_id');
        $data['recentBatches'] = PayrollBatch::withCount('payslips')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(5)
            ->get();
        $data['activeRule'] = PayrollWorkingDaysSetting::getActiveSetting();
        $data['totalMonthlyBudget'] = (float) SalaryStructure::where('status', 'Active')->sum('monthly_gross');
        $data['lastBatch'] = PayrollBatch::orderBy('year', 'desc')->orderBy('month', 'desc')->first();

        return view('payroll.process', $data);
    }

    /**
     * Generate Monthly Payroll Batch.
     */
    public function generateBatch(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020|max:2030',
        ]);

        $month = (int) $request->month;
        $year = (int) $request->year;

        try {
            $batch = $this->payrollService->generateMonthlyBatch($month, $year, auth()->id(), [
                'notes' => $request->notes ?? "Monthly payroll generated via admin portal for " . Carbon::createFromDate($year, $month, 1)->format('F Y'),
            ]);

            return redirect()->route('payroll.batches.show', $batch->id)
                ->with('success', "Payroll batch {$batch->batch_number} generated successfully for {$batch->total_employees} employees.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate payroll batch: ' . $e->getMessage());
        }
    }

    /**
     * List all Payroll Batches with search, filters, KPIs and AJAX pagination.
     */
    public function batches(Request $request)
    {
        $data['title'] = 'Payroll Batches Hub';

        $query = PayrollBatch::withCount('payslips');

        // Search Filter (Batch Number or Notes)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('batch_number', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Year Filter
        if ($request->filled('year')) {
            $query->where('year', (int) $request->year);
        }

        // Month Filter
        if ($request->filled('month')) {
            $query->where('month', (int) $request->month);
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $batches = $query->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        $batches->appends($request->all());
        $data['batches'] = $batches;

        // Executive Statistics
        $data['totalBatches'] = PayrollBatch::count();
        $data['totalDisbursed'] = (float) PayrollBatch::where('status', 'Paid')->sum('total_net_pay');
        $data['paidBatchesCount'] = PayrollBatch::where('status', 'Paid')->count();
        $data['pendingBatchesCount'] = PayrollBatch::where('status', '!=', 'Paid')->count();
        $data['totalEmployeesProcessed'] = (int) PayrollBatch::sum('total_employees');

        // Retain Filter inputs
        $data['filterSearch'] = $request->search;
        $data['filterYear'] = $request->year;
        $data['filterMonth'] = $request->month;
        $data['filterStatus'] = $request->status;

        // Available years for dropdown
        $data['availableYears'] = PayrollBatch::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');
        if ($data['availableYears']->isEmpty()) {
            $data['availableYears'] = collect([Carbon::now()->year]);
        }

        // If AJAX request, return JSON with rendered partial HTML
        if ($request->ajax() || $request->wantsJson() || $request->has('ajax')) {
            return response()->json([
                'success' => true,
                'html' => view('payroll.partials.batches_table', $data)->render(),
                'total' => $batches->total(),
                'from' => $batches->firstItem() ?? 0,
                'to' => $batches->lastItem() ?? 0,
            ]);
        }

        return view('payroll.batches', $data);
    }

    /**
     * View single Payroll Batch Details with search and status filters.
     */
    public function batchDetails(Request $request, $id)
    {
        $batch = PayrollBatch::findOrFail($id);
        $data['title'] = 'Payroll / Batch ' . $batch->batch_number;
        $data['batch'] = $batch;

        // Query Payslips with Filters
        $query = $batch->payslips()->with(['employee.designation', 'employee.agency']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('employee', function ($empQ) use ($search) {
                    $empQ->where('employee_name', 'like', "%{$search}%")
                         ->orWhere('employee_email', 'like', "%{$search}%")
                         ->orWhere('employee_id', $search);
                })->orWhere('payslip_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('lwp_status')) {
            if ($request->lwp_status === 'with_lwp') {
                $query->where('lwp_days', '>', 0);
            } elseif ($request->lwp_status === 'zero_lwp') {
                $query->where('lwp_days', '<=', 0);
            }
        }

        $payslips = $query->orderBy('id', 'asc')->get();
        $data['payslips'] = $payslips;

        // Batch Level Payment Stats
        $data['totalCount'] = $batch->payslips()->count();
        $data['paidCount'] = $batch->payslips()->where('payment_status', 'Paid')->count();
        $data['paidAmount'] = (float) $batch->payslips()->where('payment_status', 'Paid')->sum('net_salary');
        $data['pendingCount'] = $batch->payslips()->where('payment_status', 'Pending')->count();
        $data['pendingAmount'] = (float) $batch->payslips()->where('payment_status', 'Pending')->sum('net_salary');

        // Retain Filter Inputs
        $data['filterSearch'] = $request->search;
        $data['filterPaymentStatus'] = $request->payment_status;
        $data['filterLwpStatus'] = $request->lwp_status;

        // Active Working Days Master Policy
        $data['activeRule'] = PayrollWorkingDaysSetting::getActiveSetting();

        // -----------------------------------------------------------------
        // Build Attendance Register Matrix (1..31 Days) for this Batch
        // -----------------------------------------------------------------
        $month = $batch->month;
        $year = $batch->year;
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth()->startOfDay();
        $today = Carbon::today()->startOfDay();

        // Date Columns
        $matrixDates = [];
        $cursor = $startDate->copy();
        while ($cursor->lte($endDate)) {
            $matrixDates[] = [
                'date' => $cursor->toDateString(),
                'day' => $cursor->format('d'),
                'day_name' => $cursor->format('D'),
                'is_weekend' => $cursor->isWeekend(),
                'is_today' => $cursor->isToday(),
                'is_future' => $cursor->gt($today),
            ];
            $cursor->addDay();
        }
        $data['matrixDates'] = $matrixDates;

        // Holidays
        $holidays = DB::table('holidays')
            ->where(function ($q) {
                $q->where('status', 'Active')
                  ->orWhere('status', 1)
                  ->orWhere('status', '1');
            })
            ->whereDate('holiday_date', '>=', $startDate->toDateString())
            ->whereDate('holiday_date', '<=', $endDate->toDateString())
            ->pluck('holiday_name', 'holiday_date')
            ->toArray();

        // Batch Employee IDs
        $empIds = $payslips->pluck('employee_id')->filter()->unique()->toArray();

        // Approved Leaves
        $leaves = DB::table('leaves')
            ->whereIn('employee_id', $empIds)
            ->where('status', 'APPROVED')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('from_date', [$startDate->toDateString(), $endDate->toDateString()])
                  ->orWhereBetween('to_date', [$startDate->toDateString(), $endDate->toDateString()])
                  ->orWhere(function ($sub) use ($startDate, $endDate) {
                      $sub->where('from_date', '<=', $startDate->toDateString())
                          ->where('to_date', '>=', $endDate->toDateString());
                  });
            })
            ->get(['employee_id', 'from_date', 'to_date']);

        $leaveMap = [];
        foreach ($leaves as $lv) {
            $c = Carbon::parse($lv->from_date);
            $lEnd = Carbon::parse($lv->to_date);
            while ($c->lte($lEnd)) {
                if ($c->between($startDate, $endDate)) {
                    $leaveMap["{$lv->employee_id}_{$c->toDateString()}"] = true;
                }
                $c->addDay();
            }
        }

        // Attendance Punches
        $attendances = DB::table('employee_attendances')
            ->whereIn('employee_id', $empIds)
            ->whereDate('attendance_date', '>=', $startDate->toDateString())
            ->whereDate('attendance_date', '<=', $endDate->toDateString())
            ->where(function ($q) {
                $q->whereNotNull('check_in_at')
                  ->orWhereIn('status', ['checked_in', 'checked_out', 'present', 'PRESENT']);
            })
            ->get();

        $attendanceMap = [];
        foreach ($attendances as $att) {
            $dKey = Carbon::parse($att->attendance_date)->format('Y-m-d');
            $attendanceMap["{$att->employee_id}_{$dKey}"] = true;
        }

        // Initialize Daily Column Totals
        $matrixDailyTotals = [];
        foreach ($matrixDates as $d) {
            $matrixDailyTotals[$d['date']] = [
                'P' => 0,
                'A' => 0,
                'W' => 0,
                'H' => 0,
                'L' => 0,
                '-' => 0,
            ];
        }

        $matrixRows = [];
        $grandTotalPresent = 0;
        $grandTotalAbsent = 0;

        foreach ($payslips as $slip) {
            $emp = $slip->employee;
            if (!$emp) continue;

            $joiningDate = !empty($emp->joining_date) ? Carbon::parse($emp->joining_date)->startOfDay() : null;
            $empDaily = [];
            $pCount = 0;
            $aCount = 0;
            $wCount = 0;
            $hCount = 0;
            $lCount = 0;

            foreach ($matrixDates as $d) {
                $dDate = Carbon::parse($d['date'])->startOfDay();
                $hasAtt = isset($attendanceMap["{$emp->employee_id}_{$d['date']}"]);

                if ($hasAtt) {
                    $status = 'P'; // Present
                    $pCount++;
                    $matrixDailyTotals[$d['date']]['P']++;
                    $grandTotalPresent++;
                } elseif ($dDate->gt($today)) {
                    $status = '-'; // Future date
                    $matrixDailyTotals[$d['date']]['-']++;
                } elseif ($joiningDate && $dDate->lt($joiningDate)) {
                    $status = '-'; // Not yet joined
                    $matrixDailyTotals[$d['date']]['-']++;
                } elseif (isset($holidays[$d['date']])) {
                    $status = 'H'; // Holiday
                    $hCount++;
                    $matrixDailyTotals[$d['date']]['H']++;
                } elseif ($d['is_weekend']) {
                    $status = 'W'; // Weekend
                    $wCount++;
                    $matrixDailyTotals[$d['date']]['W']++;
                } elseif (isset($leaveMap["{$emp->employee_id}_{$d['date']}"])) {
                    $status = 'L'; // Leave
                    $lCount++;
                    $matrixDailyTotals[$d['date']]['L']++;
                } else {
                    $status = 'A'; // Absent / LWP
                    $aCount++;
                    $matrixDailyTotals[$d['date']]['A']++;
                    $grandTotalAbsent++;
                }

                $empDaily[$d['date']] = $status;
            }

            $totalWorkingDaysInRange = count($matrixDates) - $wCount - $hCount;
            $pct = $totalWorkingDaysInRange > 0 ? round(($pCount / $totalWorkingDaysInRange) * 100, 1) : 100;

            $matrixRows[] = [
                'payslip' => $slip,
                'employee' => $emp,
                'employee_name' => $emp->employee_name,
                'employee_email' => $emp->employee_email,
                'agency_name' => $emp->agency->agency_name ?? 'Direct / In-House',
                'designation_name' => $slip->designation_name ?? ($emp->designation->name ?? $emp->designation->Designation ?? '-'),
                'daily' => $empDaily,
                'p_count' => $pCount,
                'a_count' => $aCount,
                'w_count' => $wCount,
                'h_count' => $hCount,
                'l_count' => $lCount,
                'percentage' => $pct,
            ];
        }

        $data['matrixRows'] = $matrixRows;
        $data['matrixDailyTotals'] = $matrixDailyTotals;
        $data['grandTotalPresent'] = $grandTotalPresent;
        $data['grandTotalAbsent'] = $grandTotalAbsent;

        return view('payroll.batch_details', $data);
    }

    /**
     * Selectively Disburse / Pay specific employees via Checkboxes.
     */
    public function disburseSelected(Request $request, $id)
    {
        $request->validate([
            'payslip_ids' => 'required|array|min:1',
            'payment_date' => 'required|date',
            'payment_mode' => 'required|string',
            'transaction_reference' => 'nullable|string|max:100',
        ]);

        $batch = PayrollBatch::findOrFail($id);

        Payslip::whereIn('id', $request->payslip_ids)
            ->where('payroll_batch_id', $batch->id)
            ->update([
                'payment_status' => 'Paid',
                'payment_date' => $request->payment_date,
                'payment_mode' => $request->payment_mode,
                'transaction_reference' => $request->transaction_reference,
            ]);

        // Check if all payslips in this batch are now Paid
        $remainingPending = $batch->payslips()->where('payment_status', 'Pending')->count();
        if ($remainingPending === 0) {
            $batch->update([
                'status' => 'Paid',
                'approved_by' => auth()->id(),
                'payment_date' => $request->payment_date,
                'payment_mode' => $request->payment_mode,
                'transaction_reference' => $request->transaction_reference,
            ]);
        }

        $count = count($request->payslip_ids);
        return redirect()->back()->with('success', "{$count} Employee(s) successfully marked as Paid with transaction reference {$request->transaction_reference}.");
    }

    /**
     * Revert selected employees back to Pending payment status.
     */
    public function markPending(Request $request, $id)
    {
        $request->validate([
            'payslip_ids' => 'required|array|min:1',
        ]);

        $batch = PayrollBatch::findOrFail($id);

        Payslip::whereIn('id', $request->payslip_ids)
            ->where('payroll_batch_id', $batch->id)
            ->update([
                'payment_status' => 'Pending',
                'payment_date' => null,
                'transaction_reference' => null,
            ]);

        // If batch was marked Paid, revert it to Generated
        if ($batch->status === 'Paid') {
            $batch->update(['status' => 'Generated']);
        }

        $count = count($request->payslip_ids);
        return redirect()->back()->with('success', "{$count} Employee(s) reverted to Pending status.");
    }

    /**
     * Fetch complete monthly attendance log and register breakdown for an employee (Modal View).
     */
    public function attendanceRegisterDetails(Request $request, $employee_id, $month, $year)
    {
        $employee = Employee::with(['designation', 'agency'])->where('employee_id', $employee_id)->firstOrFail();
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth()->endOfDay();
        $totalDays = $startDate->daysInMonth;

        // Fetch Employee Monthly Attendance
        $attendances = \Illuminate\Support\Facades\DB::table('employee_attendances')
            ->where('employee_id', $employee_id)
            ->whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->keyBy('attendance_date');

        // Fetch Approved Leaves
        $approvedLeaves = \Illuminate\Support\Facades\DB::table('leaves')
            ->join('leave_types', 'leaves.leave_type_id', '=', 'leave_types.id')
            ->where('leaves.employee_id', $employee_id)
            ->where('leaves.status', 'APPROVED')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('from_date', [$startDate->toDateString(), $endDate->toDateString()])
                      ->orWhereBetween('to_date', [$startDate->toDateString(), $endDate->toDateString()]);
            })
            ->select('leaves.*', 'leave_types.leave_name')
            ->get();

        // Fetch Public Holidays
        $holidays = \Illuminate\Support\Facades\DB::table('holidays')
            ->whereBetween('holiday_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->keyBy('holiday_date');

        // Build Daily Matrix
        $dailyRecords = [];
        $presentCount = 0;
        $leaveCount = 0;
        $holidayCount = 0;
        $weeklyOffCount = 0;
        $lwpCount = 0;

        for ($day = 1; $day <= $totalDays; $day++) {
            $currentDate = Carbon::createFromDate($year, $month, $day);
            $dateStr = $currentDate->toDateString();
            $att = $attendances->get($dateStr);
            $holiday = $holidays->get($dateStr);
            $isSunday = $currentDate->isSunday();

            // Check if date falls in an approved leave
            $onLeave = $approvedLeaves->first(function ($leave) use ($dateStr) {
                return $dateStr >= $leave->from_date && $dateStr <= $leave->to_date;
            });

            $checkIn = '-';
            $checkOut = '-';
            $duration = '-';
            $statusCode = 'ABSENT';
            $statusLabel = 'Absent / LWP';
            $badgeClass = 'bg-danger';
            $remark = '-';

            if ($att && (!empty($att->check_in_at) || in_array($att->status, ['checked_in', 'checked_out', 'present', 'PRESENT']))) {
                $presentCount++;
                $statusCode = 'PRESENT';
                $statusLabel = 'Present';
                $badgeClass = 'bg-success';
                $checkIn = !empty($att->check_in_at) ? Carbon::parse($att->check_in_at)->format('h:i A') : 'Punch Recorded';
                $checkOut = !empty($att->check_out_at) ? Carbon::parse($att->check_out_at)->format('h:i A') : 'Active';

                if (!empty($att->check_in_at) && !empty($att->check_out_at)) {
                    $inT = Carbon::parse($att->check_in_at);
                    $outT = Carbon::parse($att->check_out_at);
                    $totalMinutes = max(0, (int) round($inT->diffInMinutes($outT)));
                    $hours = intdiv($totalMinutes, 60);
                    $mins = $totalMinutes % 60;
                    $duration = "{$hours}h {$mins}m";
                }
            } elseif ($onLeave) {
                $leaveCount++;
                $statusCode = 'LEAVE';
                $statusLabel = 'Approved Leave';
                $badgeClass = 'bg-info text-white';
                $remark = $onLeave->leave_name ?? 'Paid Leave';
            } elseif ($holiday && !$isSunday) {
                $holidayCount++;
                $statusCode = 'HOLIDAY';
                $statusLabel = 'Holiday';
                $badgeClass = 'bg-purple text-white';
                $remark = $holiday->holiday_name ?? 'Public Holiday';
            } elseif ($isSunday) {
                $weeklyOffCount++;
                $statusCode = 'WEEKLY_OFF';
                $statusLabel = 'Weekly Off';
                $badgeClass = 'bg-secondary';
                $remark = 'Sunday';
            } else {
                $lwpCount++;
                $remark = 'Loss of Pay';
            }

            $dailyRecords[] = [
                'day' => sprintf('%02d', $day),
                'date' => $currentDate->format('d M, Y'),
                'day_name' => $currentDate->format('l'),
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'duration' => $duration,
                'status_code' => $statusCode,
                'status_label' => $statusLabel,
                'badge_class' => $badgeClass,
                'remark' => $remark,
            ];
        }

        $payableDays = max(0, $totalDays - $lwpCount);

        return response()->json([
            'success' => true,
            'employee' => [
                'id' => $employee->employee_id,
                'name' => $employee->employee_name,
                'email' => $employee->employee_email,
                'designation' => $employee->designation->name ?? $employee->designation->Designation ?? 'Staff',
                'agency' => $employee->agency->agency_name ?? 'Operations',
            ],
            'period' => $startDate->format('F Y'),
            'metrics' => [
                'total_days' => $totalDays,
                'present_days' => $presentCount,
                'paid_leave_days' => $leaveCount,
                'weekly_off_days' => $weeklyOffCount,
                'holiday_days' => $holidayCount,
                'lwp_days' => $lwpCount,
                'payable_days' => $payableDays,
            ],
            'records' => $dailyRecords,
        ]);
    }

    /**
     * Approve and mark Payroll Batch as Paid (All payslips).
     */
    public function approveBatch(Request $request, $id)
    {
        $batch = PayrollBatch::findOrFail($id);
        
        $batch->update([
            'status' => 'Paid',
            'approved_by' => auth()->id(),
            'payment_date' => $request->payment_date ?? now()->toDateString(),
            'payment_mode' => $request->payment_mode ?? 'Bank Transfer',
            'transaction_reference' => $request->transaction_reference,
        ]);

        $batch->payslips()->update([
            'payment_status' => 'Paid',
            'payment_date' => $batch->payment_date,
            'payment_mode' => $batch->payment_mode,
            'transaction_reference' => $batch->transaction_reference,
        ]);

        return redirect()->back()->with('success', "Payroll batch {$batch->batch_number} has been approved and marked as Paid.");
    }


    /**
     * List all Payslips across employees with filters.
     */
    public function payslips(Request $request)
    {
        $data['title'] = 'Payroll / Employee Payslips';

        if ($request->ajax()) {
            $query = Payslip::with(['employee.designation', 'employee.agency', 'batch'])
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->orderBy('id', 'desc');

            if ($request->filled('month') && $request->month !== '' && $request->month !== 'all') {
                $query->where('month', (int) $request->month);
            }
            if ($request->filled('year') && $request->year !== '' && $request->year !== 'all') {
                $query->where('year', (int) $request->year);
            }
            if ($request->filled('payment_status') && $request->payment_status !== '' && $request->payment_status !== 'all') {
                $query->where('payment_status', $request->payment_status);
            }
            if ($request->filled('agency_id') && $request->agency_id !== '' && $request->agency_id !== 'all') {
                if ($request->agency_id === 'unassigned' || $request->agency_id === 'direct') {
                    $query->whereHas('employee', function ($q) {
                        $q->whereNull('agency_id');
                    });
                } else {
                    $agencyId = $request->agency_id;
                    $query->whereHas('employee', function ($q) use ($agencyId) {
                        $q->where('agency_id', $agencyId);
                    });
                }
            }

            $payslips = $query->get();

            $formatted = $payslips->map(function ($slip) {
                $emp = $slip->employee;
                $empName = $emp ? $emp->employee_name : 'Employee';
                $empEmail = $emp ? ($emp->employee_email ?? '') : '';
                $empImage = $emp ? ($emp->profile_image ?? '') : '';
                $designation = $slip->designation_name ?: ($emp && $emp->designation ? ($emp->designation->name ?? $emp->designation->Designation ?? 'Staff') : 'Staff');
                $agency = $emp && $emp->agency ? $emp->agency->agency_name : 'Direct / In-House';
                $hasAgency = $emp && !empty($emp->agency_id) && $agency !== 'Direct / In-House';
                $period = Carbon::createFromDate($slip->year, $slip->month, 1)->format('M Y');
                $periodFull = Carbon::createFromDate($slip->year, $slip->month, 1)->format('F Y');

                return [
                    'id' => $slip->id,
                    'payslip_number' => $slip->payslip_number,
                    'employee_id' => $slip->employee_id,
                    'employee_name' => $empName,
                    'employee_email' => $empEmail,
                    'profile_image' => $empImage,
                    'designation' => $designation,
                    'agency' => $agency,
                    'has_agency' => $hasAgency,
                    'period' => $period,
                    'period_full' => $periodFull,
                    'month' => $slip->month,
                    'year' => $slip->year,
                    'total_days' => (float)$slip->total_days,
                    'payable_days' => (float)$slip->payable_days,
                    'present_days' => (float)$slip->present_days,
                    'paid_leave_days' => (float)$slip->paid_leave_days,
                    'holiday_days' => (float)$slip->holiday_days,
                    'weekly_off_days' => (float)$slip->weekly_off_days,
                    'lwp_days' => (float)$slip->lwp_days,
                    'base_gross' => (float)$slip->base_gross,
                    'earned_basic' => (float)$slip->earned_basic,
                    'earned_hra' => (float)$slip->earned_hra,
                    'earned_conveyance' => (float)$slip->earned_conveyance,
                    'earned_medical' => (float)$slip->earned_medical,
                    'earned_bike_allowance' => (float)($slip->earned_bike_allowance ?? 1500),
                    'earned_special_allowance' => (float)$slip->earned_special_allowance,
                    'gratuity_accrual' => (float)($slip->gratuity_accrual ?? 0),
                    'bonus' => (float)$slip->bonus,
                    'incentive' => (float)$slip->incentive,
                    'overtime_pay' => (float)$slip->overtime_pay,
                    'total_earnings' => (float)$slip->total_earnings,
                    'pf_deduction' => (float)$slip->pf_deduction,
                    'esi_deduction' => (float)$slip->esi_deduction,
                    'pt_deduction' => (float)$slip->pt_deduction,
                    'tds_deduction' => (float)$slip->tds_deduction,
                    'lwp_deduction' => (float)$slip->lwp_deduction,
                    'other_deductions' => (float)$slip->other_deductions,
                    'total_deductions' => (float)$slip->total_deductions,
                    'net_salary' => (float)$slip->net_salary,
                    'payment_status' => $slip->payment_status ?: 'Pending',
                    'payment_date' => $slip->payment_date ? Carbon::parse($slip->payment_date)->format('d M, Y') : '-',
                    'payment_mode' => $slip->payment_mode ?: 'Bank Transfer',
                    'transaction_reference' => $slip->transaction_reference ?: '-',
                    'bank_name' => $slip->bank_name ?: '-',
                    'bank_account_no' => $slip->bank_account_no ?: '-',
                    'ifsc_code' => $slip->ifsc_code ?: '-',
                    'pan_no' => $slip->pan_no ?: '-',
                    'uan_no' => $slip->uan_no ?: '-',
                    'download_url' => route('payroll.payslips.download', $slip->id),
                ];
            });

            return response()->json(['data' => $formatted]);
        }

        $data['totalPayslips'] = Payslip::count();
        $data['totalDisbursedAmount'] = (float) Payslip::where('payment_status', 'Paid')->sum('net_salary');
        $data['totalPendingAmount'] = (float) Payslip::where('payment_status', 'Pending')->sum('net_salary');
        $data['totalGrossAmount'] = (float) Payslip::sum('total_earnings');
        $data['paidCount'] = Payslip::where('payment_status', 'Paid')->count();
        $data['pendingCount'] = Payslip::where('payment_status', 'Pending')->count();
        $data['agencies'] = \App\Models\Agency::where('status', 'Active')->orderBy('agency_name')->get();
        $data['availableYears'] = Payslip::select('year')->distinct()->orderBy('year', 'desc')->pluck('year')->toArray();
        if (empty($data['availableYears'])) {
            $data['availableYears'] = [Carbon::now()->year];
        }

        $data['selectedMonth'] = $request->month;
        $data['selectedYear'] = $request->year ?: Carbon::now()->year;

        return view('payroll.payslips', $data);
    }

    /**
     * Admin Download Payslip PDF.
     */
    public function downloadPdf($id)
    {
        $payslip = Payslip::with('employee')->findOrFail($id);

        $filename = sprintf('payslip_%d_%d_%02d.pdf', $payslip->employee_id, $payslip->year, $payslip->month);
        $fullPath = storage_path('app/public/payslips/' . $filename);

        if (!File::exists($fullPath)) {
            $relPath = $this->payrollService->generatePayslipPdf($payslip);
            $payslip->update(['pdf_path' => $relPath]);
        }

        return response()->download($fullPath, "Payslip_{$payslip->payslip_number}.pdf", [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
