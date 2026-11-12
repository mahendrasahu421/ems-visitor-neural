<?php

namespace App\Http\Controllers\Attendance;

use App\Exports\AttendanceRegisterExport;
use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\PayrollWorkingDaysSetting;
use App\Services\ShiftAttendanceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceRegisterReportController extends Controller
{
    /**
     * Display the interactive Attendance Register Report.
     */
    public function index(Request $request)
    {
        $data = $this->buildRegisterData($request);
        $data['title'] = 'Attendance Register Report';

        return view('attendance.register.index', $data);
    }

    /**
     * Export Attendance Register Report to Excel.
     */
    public function exportExcel(Request $request)
    {
        $data = $this->buildRegisterData($request);

        $cleanPeriod = str_replace([' ', '/', '\\', ':'], '_', $data['dateRangeText']);
        $cleanAgency = preg_replace('/[^A-Za-z0-9_-]+/', '_', $data['filterAgencyName']);
        $fileName = 'Attendance_Register_' . trim($cleanAgency, '_') . '_' . $cleanPeriod . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new AttendanceRegisterExport($data), $fileName);
    }

    /**
     * Export Attendance Register Report to PDF.
     */
    public function exportPdf(Request $request)
    {
        $data = $this->buildRegisterData($request);

        $cleanPeriod = str_replace([' ', '/', '\\', ':'], '_', $data['dateRangeText']);
        $cleanAgency = preg_replace('/[^A-Za-z0-9_-]+/', '_', $data['filterAgencyName']);
        $fileName = 'Attendance_Register_' . trim($cleanAgency, '_') . '_' . $cleanPeriod . '_' . now()->format('Ymd_His') . '.pdf';

        $pdf = Pdf::loadView('attendance.register.pdf', $data)
            ->setPaper('a4', 'landscape')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);

        return $pdf->download($fileName);
    }

    /**
     * Common data aggregation engine for web view, Excel, and PDF.
     */
    public function buildRegisterData(Request $request): array
    {
        // 1. Determine Date Range
        $fromDateInput = $request->input('from_date');
        $toDateInput = $request->input('to_date');
        $monthInput = $request->input('month');

        if ($fromDateInput && $toDateInput) {
            try {
                $start = Carbon::parse($fromDateInput)->startOfDay();
                $end = Carbon::parse($toDateInput)->startOfDay();
                if ($end->lt($start)) {
                    $tmp = $start;
                    $start = $end;
                    $end = $tmp;
                }
            } catch (\Exception $e) {
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
            }
            $selectedMonth = $start->format('Y-m');
            $dateRangeText = $start->format('d M, Y') . ' - ' . $end->format('d M, Y');
        } else {
            $month = $monthInput ?: Carbon::now()->format('Y-m');
            try {
                $start = Carbon::parse($month . '-01')->startOfDay();
                $end = $start->copy()->endOfMonth()->startOfDay();
            } catch (\Exception $e) {
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
            }
            $selectedMonth = $start->format('Y-m');
            $dateRangeText = $start->format('F Y');
        }

        // Limit range span to reasonable max (e.g. 31 days) for matrix display
        if ($start->diffInDays($end) > 31) {
            $end = $start->copy()->addDays(31);
            $dateRangeText = $start->format('d M, Y') . ' - ' . $end->format('d M, Y');
        }

        // 2. Build Date Columns list
        $dates = [];
        $cursor = $start->copy();
        $today = Carbon::today()->startOfDay();
        $activePolicy = PayrollWorkingDaysSetting::getActiveSetting();

        while ($cursor->lte($end)) {
            $isFuture = $cursor->copy()->startOfDay()->gt($today);
            $dates[] = [
                'date'       => $cursor->toDateString(),
                'day'        => $cursor->format('d'),
                'day_name'   => $cursor->format('D'),
                'is_weekend' => ShiftAttendanceService::isWeekOff($cursor, null, null, $activePolicy),
                'is_today'   => $cursor->isToday(),
                'is_future'  => $isFuture,
            ];
            $cursor->addDay();
        }

        // 3. Query Active Holidays in Range
        $holidays = Holiday::where(function ($q) {
            $q->where('status', 'Active')
                ->orWhere('status', 1)
                ->orWhere('status', '1');
        })
        ->whereDate('holiday_date', '>=', $start->toDateString())
        ->whereDate('holiday_date', '<=', $end->toDateString())
        ->pluck('holiday_name', 'holiday_date')
        ->toArray();

        // 4. Query Employees matching filters
        $empQuery = Employee::with(['designation', 'agency', 'shift'])
            ->whereNull('deleted_at');

        $staffStatus = $request->input('staff_status', 'active');
        if ($staffStatus === 'active' || empty($staffStatus)) {
            $empQuery->where(function ($q) {
                $q->where('status', 'Active')
                    ->orWhere('status', '1')
                    ->orWhereNull('status')
                    ->orWhere('status', '');
            });
        } elseif ($staffStatus === 'inactive') {
            $empQuery->where(function ($q) {
                $q->where('status', 'Inactive')
                    ->orWhere('status', '0');
            });
        }

        // Filter: Agency
        $selectedAgency = $request->input('agency_id');
        $filterAgencyName = 'All Agencies';
        if ($selectedAgency) {
            if ($selectedAgency === 'unassigned') {
                $empQuery->whereNull('agency_id');
                $filterAgencyName = 'Direct / In-House';
            } else {
                $empQuery->where('agency_id', $selectedAgency);
                $agencyObj = Agency::find($selectedAgency);
                if ($agencyObj) {
                    $filterAgencyName = $agencyObj->agency_name;
                }
            }
        }

        // Filter: Employee
        $selectedEmployee = $request->input('employee_id');
        $filterEmployeeName = 'All Employees';
        if ($selectedEmployee && $selectedEmployee !== 'all') {
            $empQuery->where('employee_id', $selectedEmployee);
            $singleEmp = Employee::find($selectedEmployee);
            if ($singleEmp) {
                $filterEmployeeName = $singleEmp->employee_name;
            }
        }

        // Filter: Supervisor
        $selectedSupervisor = $request->input('supervisor_id');
        $filterSupervisorName = 'All Supervisors';
        if ($selectedSupervisor && $selectedSupervisor !== 'all') {
            $empQuery->where('supervisor_id', $selectedSupervisor);
            $supEmp = Employee::find($selectedSupervisor);
            if ($supEmp) {
                $filterSupervisorName = $supEmp->employee_name;
            }
        }

        // Scope: Attendance Filter (Default: 'all')
        $attendanceScope = $request->input('attendance_scope', 'all');

        $checkedInEmpIdsInRange = EmployeeAttendance::whereDate('attendance_date', '>=', $start->toDateString())
            ->whereDate('attendance_date', '<=', $end->toDateString())
            ->distinct()
            ->pluck('employee_id')
            ->toArray();

        $checkedInTodayEmpIds = EmployeeAttendance::whereDate('attendance_date', Carbon::today()->toDateString())
            ->distinct()
            ->pluck('employee_id')
            ->toArray();

        // Base total counts for UI toggle pills & selector
        $baseCountQuery = clone $empQuery;
        $totalOrgEmployeesCount = (clone $baseCountQuery)->count();
        $totalCheckedInPeriodCount = (clone $baseCountQuery)->whereIn('employee_id', $checkedInEmpIdsInRange)->count();
        $totalCheckedInTodayCount = (clone $baseCountQuery)->whereIn('employee_id', $checkedInTodayEmpIds)->count();

        if ($attendanceScope === 'checked_in') {
            // Default: show only employees with check-in records in this period
            $empQuery->whereIn('employee_id', $checkedInEmpIdsInRange);
        } elseif ($attendanceScope === 'today') {
            // Show only employees who checked in today
            $empQuery->whereIn('employee_id', $checkedInTodayEmpIds);
        } elseif ($attendanceScope === 'not_checked_in') {
            // Show only employees who have not checked in in this period
            $empQuery->whereNotIn('employee_id', $checkedInEmpIdsInRange);
        }
        // If 'all', do not filter by attendance, show all matching employees

        $employees = $empQuery->orderBy('employee_name')->get();
        $employeeIds = $employees->pluck('employee_id')->toArray();

        // 5. Query Approved Leaves in Range for these employees
        $leaves = Leave::whereIn('employee_id', $employeeIds)
            ->where('status', 'APPROVED')
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('from_date', [$start->toDateString(), $end->toDateString()])
                    ->orWhereBetween('to_date', [$start->toDateString(), $end->toDateString()])
                    ->orWhere(function ($sub) use ($start, $end) {
                        $sub->where('from_date', '<=', $start->toDateString())
                            ->where('to_date', '>=', $end->toDateString());
                    });
            })
            ->get(['employee_id', 'from_date', 'to_date']);

        $leaveMap = [];
        foreach ($leaves as $lv) {
            $c = Carbon::parse($lv->from_date);
            $lEnd = Carbon::parse($lv->to_date);
            while ($c->lte($lEnd)) {
                if ($c->between($start, $end)) {
                    $leaveMap["{$lv->employee_id}_{$c->toDateString()}"] = true;
                }
                $c->addDay();
            }
        }

        // 6. Query Attendance Records in Range
        $attendances = EmployeeAttendance::whereIn('employee_id', $employeeIds)
            ->whereDate('attendance_date', '>=', $start->toDateString())
            ->whereDate('attendance_date', '<=', $end->toDateString())
            ->get();

        $attendanceMap = [];
        foreach ($attendances as $att) {
            $dateKey = Carbon::parse($att->attendance_date)->format('Y-m-d');
            $attendanceMap["{$att->employee_id}_{$dateKey}"] = true;
        }

        // 7. Build Employee Rows and Daily Totals
        $rows = [];
        $dailyTotals = [];
        foreach ($dates as $d) {
            $dailyTotals[$d['date']] = [
                'P' => 0,
                'A' => 0,
                'W' => 0,
                'H' => 0,
                'L' => 0,
                '-' => 0,
            ];
        }

        $grandTotalPresent = 0;
        $grandTotalAbsent = 0;
        $grandTotalWeekend = 0;
        $grandTotalHoliday = 0;
        $grandTotalLeave = 0;

        foreach ($employees as $emp) {
            $empAttendance = [];
            $presentCount = 0;
            $absentCount = 0;
            $weekendCount = 0;
            $holidayCount = 0;
            $leaveCount = 0;
            $futureCount = 0;

            $joiningDate = !empty($emp->joining_date) ? Carbon::parse($emp->joining_date)->startOfDay() : null;

            foreach ($dates as $d) {
                $dDate = Carbon::parse($d['date'])->startOfDay();
                $hasAtt = isset($attendanceMap["{$emp->employee_id}_{$d['date']}"]);

                $isEmpWeekOff = ShiftAttendanceService::isWeekOff($dDate, $emp, $emp->shift, $activePolicy);

                if ($hasAtt) {
                    $status = 'P'; // Present
                } elseif ($dDate->gt($today)) {
                    $status = '-'; // Future date: Not yet arrived, cannot be absent!
                } elseif ($joiningDate && $dDate->lt($joiningDate)) {
                    $status = '-'; // Date is prior to employee joining
                } elseif (isset($holidays[$d['date']])) {
                    $status = 'H'; // Gazetted / Organization Holiday
                } elseif ($isEmpWeekOff) {
                    $status = 'W'; // Weekly Off / Configured Week-Off
                } elseif (isset($leaveMap["{$emp->employee_id}_{$d['date']}"])) {
                    $status = 'L'; // Approved Leave
                } else {
                    $status = 'A'; // Truly Absent on a working day
                }

                $empAttendance[$d['date']] = $status;

                if ($status === 'P') {
                    $presentCount++;
                    $dailyTotals[$d['date']]['P']++;
                    $grandTotalPresent++;
                } elseif ($status === 'A') {
                    $absentCount++;
                    $dailyTotals[$d['date']]['A']++;
                    $grandTotalAbsent++;
                } elseif ($status === 'W') {
                    $weekendCount++;
                    $dailyTotals[$d['date']]['W']++;
                    $grandTotalWeekend++;
                } elseif ($status === 'H') {
                    $holidayCount++;
                    $dailyTotals[$d['date']]['H']++;
                    $grandTotalHoliday++;
                } elseif ($status === 'L') {
                    $leaveCount++;
                    $dailyTotals[$d['date']]['L']++;
                    $grandTotalLeave++;
                } else {
                    $futureCount++;
                    $dailyTotals[$d['date']]['-']++;
                }
            }

            // Working days elapsed so far (for percentage)
            $effectiveWorkingDays = $presentCount + $absentCount;
            $percentage = $effectiveWorkingDays > 0 ? round(($presentCount / $effectiveWorkingDays) * 100, 1) : ($presentCount > 0 ? 100 : 0);

            $rows[] = [
                'employee_id'           => $emp->employee_id,
                'employee_name'         => $emp->employee_name,
                'employee_email'        => $emp->employee_email,
                'agency_id'             => $emp->agency_id,
                'agency_name'           => $emp->agency->agency_name ?? 'Direct / In-House',
                'designation_name'      => $emp->designation->name ?? ($emp->designation->designation_name ?? 'Staff'),
                'attendance'            => $empAttendance,
                'total_present'         => $presentCount,
                'total_absent'          => $absentCount,
                'total_weekend'         => $weekendCount,
                'total_holiday'         => $holidayCount,
                'total_leave'           => $leaveCount,
                'total_future'          => $futureCount,
                'total_days'            => count($dates),
                'percentage'            => $percentage,
            ];
        }

        // Master lists for dropdown filters
        $agencies = Agency::orderBy('agency_name')->get();
        $allEmpQuery = Employee::whereNull('deleted_at')->orderBy('employee_name');
        if ($staffStatus === 'active' || empty($staffStatus)) {
            $allEmpQuery->where(function ($q) {
                $q->where('status', 'Active')
                    ->orWhere('status', '1')
                    ->orWhereNull('status')
                    ->orWhere('status', '');
            });
        } elseif ($staffStatus === 'inactive') {
            $allEmpQuery->where(function ($q) {
                $q->where('status', 'Inactive')
                    ->orWhere('status', '0');
            });
        }
        $allEmployees = $allEmpQuery->get(['employee_id', 'employee_name', 'agency_id']);

        return [
            'dates'                     => $dates,
            'rows'                      => $rows,
            'dailyTotals'               => $dailyTotals,
            'grandTotalPresent'         => $grandTotalPresent,
            'grandTotalAbsent'          => $grandTotalAbsent,
            'grandTotalWeekend'         => $grandTotalWeekend,
            'grandTotalHoliday'         => $grandTotalHoliday,
            'grandTotalLeave'           => $grandTotalLeave,
            'dateRangeText'             => $dateRangeText,
            'selectedMonth'             => $selectedMonth,
            'fromDate'                  => $fromDateInput ?: '',
            'toDate'                    => $toDateInput ?: '',
            'selectedAgency'            => $selectedAgency,
            'selectedEmployee'          => $selectedEmployee,
            'selectedSupervisor'        => $selectedSupervisor,
            'filterAgencyName'          => $filterAgencyName,
            'filterEmployeeName'        => $filterEmployeeName,
            'filterSupervisorName'      => $filterSupervisorName,
            'staffStatus'               => $staffStatus,
            'attendanceScope'           => $attendanceScope,
            'totalOrgEmployeesCount'    => $totalOrgEmployeesCount,
            'totalCheckedInPeriodCount' => $totalCheckedInPeriodCount,
            'totalCheckedInTodayCount'  => $totalCheckedInTodayCount,
            'holidays'                  => $holidays,
            'agencies'                  => $agencies,
            'allEmployees'              => $allEmployees,
            'supervisors'               => Employee::where('is_supervisor', 1)->orWhere('is_supervisor', '1')->orderBy('employee_name')->get(['employee_id', 'employee_name']),
        ];
    }
}
