<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use App\Models\Leave;
use App\Models\Holiday;
use App\Services\ShiftAttendanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceOrgReportController extends Controller
{
    private function activeEmployeeScope($query)
    {
        return $query->whereNull('deleted_at')
            ->where(function ($q) {
                $q->where('status', 'Active')
                    ->orWhere('status', 1)
                    ->orWhere('status', '1')
                    ->orWhere('status', 'active')
                    ->orWhere('status', '')
                    ->orWhereNull('status');
            });
    }

    private function isEmployeeActive(?Employee $employee): bool
    {
        if (!$employee || !empty($employee->deleted_at)) {
            return false;
        }

        $status = strtolower(trim((string) $employee->status));

        return in_array($status, ['active', '1', ''], true);
    }

    public function index(Request $request)
    {
      
        $from = Carbon::now()->startOfMonth();
        $to = Carbon::now()->endOfMonth();
        $today = Carbon::today();

        if (!$request->has('staff_status')) {
            $request->merge(['staff_status' => 'active']);
        }

        $activeEmpQuery = $this->activeEmployeeScope(Employee::query());
        $activeEmpIds = (clone $activeEmpQuery)->pluck('employee_id');

        $data['title'] = 'Employee Check-in / Check-out Report';
        $data['employees'] = (clone $activeEmpQuery)
            ->orderBy('employee_name')
            ->get(['employee_id', 'employee_name', 'employee_email']);
        $data['totalEmployees'] = $activeEmpIds->count();
        $data['supervisors'] = Employee::where('is_supervisor', 1)
            ->orWhere('is_supervisor', '1')
            ->orderBy('employee_name')
            ->get(['employee_id', 'employee_name']);

        // Today's attendance records with shift information
        $todayAttendances = EmployeeAttendance::whereIn('employee_id', $activeEmpIds)
            ->whereDate('attendance_date', $today)
            ->with(['employee.shift'])
            ->get();

        $todayLateCount = 0;
        $todayEarlyCount = 0;
        $todayEarlyComerCount = 0;
        $todayOnTimeCount = 0;

        foreach ($todayAttendances as $att) {
            $shift = $att->employee?->shift ?: ShiftAttendanceService::getDefaultShift();
            $eval = ShiftAttendanceService::evaluate($att->check_in_at, $att->check_out_at, $shift, $att->attendance_date);
            if ($eval['is_late']) {
                $todayLateCount++;
            } else {
                $todayOnTimeCount++;
            }
            if (!empty($eval['is_early_in'])) {
                $todayEarlyComerCount++;
            }
            if ($eval['is_early_out']) {
                $todayEarlyCount++;
            }
        }

        $data['checkedInToday'] = $todayAttendances->count();
        $data['currentlyInside'] = $todayAttendances->whereNull('check_out_at')->count();
        $data['checkedOutToday'] = $todayAttendances->whereNotNull('check_out_at')->count();
        $data['todayLateCount'] = $todayLateCount;
        $data['todayEarlyCount'] = $todayEarlyCount;
        $data['todayEarlyComerCount'] = $todayEarlyComerCount;
        $data['todayOnTimeCount'] = $todayOnTimeCount;

        $data['monthRecords'] = EmployeeAttendance::whereIn('employee_id', $activeEmpIds)
            ->whereBetween('attendance_date', [$from->toDateString(), $to->toDateString()])
            ->count();

        // Build Register View Data (Matrix with P/A status, agency, and employee lists)
        $registerController = new AttendanceRegisterReportController();
        $registerData = $registerController->buildRegisterData($request);
        $data = array_merge($data, $registerData);

        // View mode: default to 'register' or 'logs'
        $data['viewMode'] = $request->input('view', $request->filled('punctuality') ? 'logs' : 'register');

        return view('home.attendance.org-reports.index', $data);
    }

    public function list(Request $request)
    {
        $query = EmployeeAttendance::with(['employee.designation', 'employee.shift'])->latest('attendance_date')->latest('id');

        // Staff Status Filter (Default: 'active')
        $staffStatus = $request->input('staff_status', 'active');
        if ($staffStatus === 'active') {
            $query->whereHas('employee', function ($empQuery) {
                $this->activeEmployeeScope($empQuery);
            });
        } elseif ($staffStatus === 'inactive') {
            $query->whereHas('employee', function ($empQuery) {
                $empQuery->where(function ($q) {
                    $q->where('status', 'Inactive')
                        ->orWhere('status', 0)
                        ->orWhere('status', '0')
                        ->orWhere('status', 'inactive')
                        ->orWhereNotNull('deleted_at');
                });
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('attendance_date', '>=', Carbon::parse($request->from_date)->toDateString());
        }

        if ($request->filled('to_date')) {
            $query->whereDate('attendance_date', '<=', Carbon::parse($request->to_date)->toDateString());
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('supervisor_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('supervisor_id', $request->supervisor_id);
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'checked_in') {
                $query->where(function ($q) {
                    $q->where('status', 'checked_in')->orWhereNull('check_out_at');
                });
            } elseif ($request->status === 'checked_out') {
                $query->where(function ($q) {
                    $q->where('status', 'checked_out')->orWhereNotNull('check_out_at');
                });
            }
        }

        // Punctuality Filter (Late In, Early In, Early Out, On Time)
        if ($request->filled('punctuality')) {
            $punctuality = $request->input('punctuality');
            if (in_array($punctuality, ['late', 'early_in', 'early_out', 'on_time'], true)) {
                $query->leftJoin('employees as pe', 'employee_attendances.employee_id', '=', 'pe.employee_id')
                      ->leftJoin('shifts as ps', 'pe.shift_id', '=', 'ps.id');

                if ($punctuality === 'late') {
                    $query->whereRaw("TIME(employee_attendances.check_in_at) > ADDTIME(COALESCE(ps.start_time, '09:30:00'), SEC_TO_TIME(COALESCE(ps.grace_period, 15) * 60))");
                } elseif ($punctuality === 'early_in') {
                    $query->whereNotNull('employee_attendances.check_in_at')
                          ->whereRaw("TIME(employee_attendances.check_in_at) < COALESCE(ps.start_time, '09:30:00')");
                } elseif ($punctuality === 'early_out') {
                    $query->whereNotNull('employee_attendances.check_out_at')
                          ->whereRaw("TIME(employee_attendances.check_out_at) < COALESCE(ps.end_time, '18:30:00')");
                } elseif ($punctuality === 'on_time') {
                    $query->whereNotNull('employee_attendances.check_in_at')
                          ->whereRaw("TIME(employee_attendances.check_in_at) <= ADDTIME(COALESCE(ps.start_time, '09:30:00'), SEC_TO_TIME(COALESCE(ps.grace_period, 15) * 60))");
                }
                $query->select('employee_attendances.*');
            }
        }

        $search = $request->input('search.value');
        if ($search) {
            $query->where(function ($sub) use ($search) {
                $sub->where('note', 'like', "%{$search}%")
                    ->orWhere('check_in_ip', 'like', "%{$search}%")
                    ->orWhere('check_out_ip', 'like', "%{$search}%")
                    ->orWhereHas('employee', function ($employee) use ($search) {
                        $employee->where('employee_name', 'like', "%{$search}%")
                            ->orWhere('employee_email', 'like', "%{$search}%")
                            ->orWhere('employee_mobile', 'like', "%{$search}%");
                    });
            });
        }

        $recordsFiltered = $query->count();
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        $records = $query->skip($start)->take($length)->get();

        $rows = $records->map(function (EmployeeAttendance $attendance, $index) use ($start) {
            $checkIn = $attendance->check_in_at;
            $checkOut = $attendance->check_out_at;
            $minutes = ($checkIn && $checkOut) ? $checkIn->diffInMinutes($checkOut) : null;
            $empName = (string) ($attendance->employee?->employee_name ?? 'Unknown Employee');
            
            $initials = collect(explode(' ', trim($empName)))
                ->filter()
                ->map(fn($part) => strtoupper(substr($part, 0, 1)))
                ->take(2)
                ->join('');
            if (empty($initials)) {
                $initials = 'EM';
            }

            $isCheckedOut = !empty($checkOut) || $attendance->status === 'checked_out';
            $isEmpActive = $this->isEmployeeActive($attendance->employee);

            $shift = $attendance->employee?->shift ?: ShiftAttendanceService::getDefaultShift();
            $eval = ShiftAttendanceService::evaluate($attendance->check_in_at, $attendance->check_out_at, $shift, $attendance->attendance_date);

            return [
                'DT_RowIndex' => $start + $index + 1,
                'id' => $attendance->id,
                'date' => optional($attendance->attendance_date)->format('d M Y') ?? '-',
                'day_name' => optional($attendance->attendance_date)->format('l') ?? '',
                'employee' => $empName,
                'employee_id' => $attendance->employee_id,
                'avatar_text' => $initials,
                'email' => $attendance->employee?->employee_email ?? '-',
                'mobile' => $attendance->employee?->employee_mobile ?? '',
                'designation' => $attendance->employee?->designation?->name ?? 'Staff',
                'employee_active' => $isEmpActive,
                'shift_name' => $shift->shift_name ?? 'General Shift',
                'shift_timing' => $eval['shift_timing'],
                'shift_grace' => $eval['grace_period'],
                'check_in' => optional($checkIn)->format('h:i A') ?? '-',
                'check_in_ip' => $attendance->check_in_ip,
                'check_in_lat' => $attendance->check_in_latitude,
                'check_in_lng' => $attendance->check_in_longitude,
                'is_late' => $eval['is_late'],
                'late_minutes' => $eval['late_minutes'],
                'late_formatted' => $eval['late_formatted'],
                'late_badge' => $eval['late_badge'],
                'check_out' => optional($checkOut)->format('h:i A') ?? '-',
                'check_out_ip' => $attendance->check_out_ip,
                'check_out_lat' => $attendance->check_out_latitude,
                'check_out_lng' => $attendance->check_out_longitude,
                'is_early_out' => $eval['is_early_out'],
                'early_minutes' => $eval['early_minutes'],
                'early_formatted' => $eval['early_formatted'],
                'early_badge' => $eval['early_badge'],
                'total_minutes' => $minutes,
                'working_hours' => $minutes !== null
                    ? sprintf('%02dh %02dm', intdiv($minutes, 60), $minutes % 60)
                    : ($checkIn ? 'In Progress' : '-'),
                'status' => $isCheckedOut ? 'Checked Out' : 'In Office',
                'raw_status' => $isCheckedOut ? 'checked_out' : 'checked_in',
                'note' => $attendance->note ?: '-',
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => EmployeeAttendance::count(),
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
    }

    public function employeeMonthlyReport(Request $request, int $employeeId)
    {
        $report = $this->buildEmployeeAttendanceReport($request, $employeeId);

        if (!$report) {
            return response()->json(['ok' => false, 'message' => 'Employee not found.'], 404);
        }

        return response()->json(['ok' => true] + $report);
    }

    public function employeeDetails(Request $request, int $employeeId)
    {
        $report = $this->buildEmployeeAttendanceReport($request, $employeeId);

        if (!$report) {
            abort(404, 'Employee not found.');
        }

        return view('home.attendance.org-reports.employee-details', [
            'title' => 'Employee Attendance Details',
            ...$report,
        ]);
    }

    private function buildEmployeeAttendanceReport(Request $request, int $employeeId): ?array
    {
        $employee = Employee::with(['designation', 'agency', 'shift'])->where('employee_id', $employeeId)->first();

        if (!$employee) {
            return null;
        }

        $empShift = $employee->shift ?: ShiftAttendanceService::getDefaultShift();

        $monthInput = $request->input('month', Carbon::now()->format('Y-m'));
        if (is_numeric($monthInput)) {
            $monthNumber = max(1, min(12, (int) $monthInput));
            $yearNumber = max(2000, min(2100, (int) $request->input('year', Carbon::now()->year)));
            $monthDate = Carbon::createFromDate($yearNumber, $monthNumber, 1)->startOfMonth();
        } else {
            try {
                $monthDate = Carbon::createFromFormat('Y-m', $monthInput)->startOfMonth();
            } catch (\Exception $e) {
                $monthDate = Carbon::now()->startOfMonth();
            }
        }

        $startOfMonth = $monthDate->copy()->startOfMonth();
        $endOfMonth = $monthDate->copy()->endOfMonth();
        $fromDate = $request->filled('from_date')
            ? Carbon::parse($request->from_date)->startOfDay()
            : $startOfMonth->copy();
        $toDate = $request->filled('to_date')
            ? Carbon::parse($request->to_date)->endOfDay()
            : $endOfMonth->copy();

        if ($fromDate->lt($startOfMonth)) {
            $fromDate = $startOfMonth->copy();
        }
        if ($toDate->gt($endOfMonth)) {
            $toDate = $endOfMonth->copy();
        }
        if ($fromDate->gt($toDate)) {
            $fromDate = $startOfMonth->copy();
            $toDate = $endOfMonth->copy();
        }

        $statusFilter = strtolower((string) $request->input('status', 'all'));
        $daysInMonth = $endOfMonth->day;
        $today = Carbon::today();
        $todayStr = $today->toDateString();

        $attendances = EmployeeAttendance::where('employee_id', $employeeId)
            ->whereBetween('attendance_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->get()
            ->keyBy(fn($item) => Carbon::parse($item->attendance_date)->toDateString());

        $leaves = Leave::with('leaveType')
            ->where('employee_id', $employeeId)
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('from_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
                    ->orWhereBetween('to_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
                    ->orWhere(function ($sub) use ($startOfMonth, $endOfMonth) {
                        $sub->where('from_date', '<=', $startOfMonth->toDateString())
                            ->where('to_date', '>=', $endOfMonth->toDateString());
                    });
            })
            ->whereIn('status', ['APPROVED', 'Approved', 'approved'])
            ->get();

        $holidays = Holiday::whereBetween('holiday_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->where(function ($query) {
                $query->where('status', 1)
                    ->orWhere('status', '1')
                    ->orWhere('status', 'active')
                    ->orWhere('status', 'Active');
            })
            ->get()
            ->keyBy(fn($holiday) => Carbon::parse($holiday->holiday_date)->toDateString());

        $days = [];
        $monthMatrixDays = [];
        $pCount = 0; $lCount = 0; $hCount = 0; $wCount = 0; $aCount = 0;
        $lateCount = 0; $earlyOutCount = 0;
        $totalMinutes = 0;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = $startOfMonth->copy()->day($day);
            $dateStr = $currentDate->toDateString();
            $isWeekOff = ShiftAttendanceService::isWeekOff($currentDate, $employee, $empShift);
            $isSunday = $currentDate->isSunday();
            $isWeekend = $currentDate->isWeekend();
            $isFuture = $dateStr > $todayStr;
            $isToday = $dateStr === $todayStr;

            $att = $attendances->get($dateStr);
            $holiday = $holidays->get($dateStr);

            $leave = $leaves->first(function ($l) use ($dateStr) {
                return $dateStr >= $l->from_date && $dateStr <= $l->to_date;
            });

            $checkIn = $att?->check_in_at;
            $checkOut = $att?->check_out_at;
            $minutes = ($checkIn && $checkOut) ? $checkIn->diffInMinutes($checkOut) : null;
            if ($minutes !== null) {
                $totalMinutes += $minutes;
            }

            $details = null;
            $letterCode = '-';
            $eval = null;

            if ($att && $checkIn) {
                $type = 'present';
                $letterCode = 'P';
                $badgeText = $checkOut ? 'Present' : 'In Office (Active)';
                $badgeClass = $checkOut ? 'bg-success-subtle text-success border-success-subtle' : 'bg-primary-subtle text-primary border-primary-subtle';
                $pCount++;

                $eval = ShiftAttendanceService::evaluate($checkIn, $checkOut, $empShift, $dateStr);
                if ($eval['is_late']) {
                    $lateCount++;
                }
                if ($eval['is_early_out']) {
                    $earlyOutCount++;
                }
            } elseif ($leave) {
                $type = 'leave';
                $letterCode = 'L';
                $leaveName = $leave->leaveType?->leave_name ?? 'Leave';
                $badgeText = 'Leave: ' . $leaveName;
                $badgeClass = 'bg-info-subtle text-info border-info-subtle';
                $details = $leave->reason ?: 'Approved Leave';
                $lCount++;
            } elseif ($holiday) {
                $type = 'holiday';
                $letterCode = 'H';
                $badgeText = 'Festival / Holiday: ' . $holiday->holiday_name;
                $badgeClass = 'bg-info-subtle text-info border-info-subtle';
                $details = $holiday->holiday_remark ?: 'Official Holiday';
                $hCount++;
            } elseif ($isWeekOff) {
                $type = 'weekly_off';
                $letterCode = 'WO';
                $dayNameStr = $currentDate->format('l');
                $badgeText = "{$dayNameStr} (Weekly Off)";
                $badgeClass = 'bg-warning-subtle text-warning border-warning-subtle';
                $wCount++;
            } elseif ($isFuture) {
                $type = 'future';
                $letterCode = '-';
                $badgeText = 'Upcoming';
                $badgeClass = 'bg-light text-muted border';
            } else {
                $type = 'absent';
                $letterCode = 'A';
                $badgeText = 'Absent';
                $badgeClass = 'bg-danger-subtle text-danger border-danger-subtle';
                $aCount++;
            }

            $dayRow = [
                'day' => $day,
                'date' => $dateStr,
                'date_formatted' => $currentDate->format('d M Y'),
                'day_name' => $currentDate->format('l'),
                'day_short' => $currentDate->format('D'),
                'is_today' => $isToday,
                'is_sunday' => $isSunday,
                'is_week_off' => $isWeekOff,
                'is_weekend' => $isWeekend,
                'is_future' => $isFuture,
                'type' => $type,
                'letter_code' => $letterCode,
                'badge_text' => $badgeText,
                'badge_class' => $badgeClass,
                'shift_name' => $empShift->shift_name ?? 'General Shift',
                'shift_timing' => $eval['shift_timing'] ?? '',
                'is_late' => $eval['is_late'] ?? false,
                'late_minutes' => $eval['late_minutes'] ?? 0,
                'late_formatted' => $eval['late_formatted'] ?? '',
                'late_badge' => $eval['late_badge'] ?? '',
                'is_early_out' => $eval['is_early_out'] ?? false,
                'early_minutes' => $eval['early_minutes'] ?? 0,
                'early_formatted' => $eval['early_formatted'] ?? '',
                'early_badge' => $eval['early_badge'] ?? '',
                'check_in' => $checkIn ? $checkIn->format('h:i A') : '-',
                'check_in_ip' => $att?->check_in_ip,
                'check_out' => $checkOut ? $checkOut->format('h:i A') : ($checkIn ? 'In Progress' : '-'),
                'check_out_ip' => $att?->check_out_ip,
                'working_hours' => $minutes !== null ? sprintf('%02dh %02dm', intdiv($minutes, 60), $minutes % 60) : ($checkIn ? 'In Progress' : '-'),
                'note' => $att?->note ?: ($details ?: '-'),
            ];

            // Always add to month-level matrix strip
            $monthMatrixDays[] = $dayRow;

            if ($currentDate->lt($fromDate) || $currentDate->gt($toDate)) {
                continue;
            }

            if ($statusFilter !== 'all' && $type !== $statusFilter) {
                continue;
            }

            $days[] = $dayRow;
        }

        $initials = collect(explode(' ', trim($employee->employee_name)))
            ->filter()
            ->map(fn($part) => strtoupper(substr($part, 0, 1)))
            ->take(2)
            ->join('');

        return [
            'employee' => [
                'id' => $employee->employee_id,
                'name' => $employee->employee_name,
                'email' => $employee->employee_email,
                'mobile' => $employee->employee_mobile,
                'designation' => $employee->designation?->name ?? 'Staff',
                'agency_id' => $employee->agency_id,
                'agency_name' => $employee->agency?->agency_name ?? 'Direct / In-House',
                'agency_code' => $employee->agency?->agency_code,
                'shift' => [
                    'name' => $empShift->shift_name ?? 'General Shift',
                    'timing' => date('h:i A', strtotime($empShift->start_time ?? '09:30:00')) . ' - ' . date('h:i A', strtotime($empShift->end_time ?? '18:30:00')),
                    'grace' => $empShift->grace_period ?? 15,
                ],
                'active' => $this->isEmployeeActive($employee),
                'status_label' => $this->isEmployeeActive($employee) ? 'Active' : 'Inactive',
                'avatar_text' => $initials ?: 'EM',
            ],
            'period' => [
                'month' => $monthDate->format('Y-m'),
                'month_number' => (int) $monthDate->format('m'),
                'year' => (int) $monthDate->format('Y'),
                'month_name' => $startOfMonth->format('F Y'),
                'start_date' => $startOfMonth->toDateString(),
                'end_date' => $endOfMonth->toDateString(),
                'from_date' => $fromDate->toDateString(),
                'to_date' => $toDate->toDateString(),
                'status' => $statusFilter,
            ],
            'summary' => [
                'total_days' => $daysInMonth,
                'present_days' => $pCount,
                'late_days' => $lateCount,
                'early_out_days' => $earlyOutCount,
                'leave_days' => $lCount,
                'holiday_days' => $hCount,
                'weekly_off_days' => $wCount,
                'sunday_days' => $wCount,
                'absent_days' => $aCount,
                'total_work_minutes' => $totalMinutes,
                'total_work_hours' => sprintf('%02dh %02dm', intdiv($totalMinutes, 60), $totalMinutes % 60),
                'avg_daily_hours' => $pCount > 0 ? sprintf('%02dh %02dm', intdiv(intdiv($totalMinutes, $pCount), 60), intdiv($totalMinutes, $pCount) % 60) : '00h 00m',
            ],
            'days' => $days,
            'month_matrix_days' => $monthMatrixDays,
        ];
    }

    public function create()
    {
        $data['title'] = 'Masters/Organisation/Attendance Reports';
        $data['imageUrl'] = "https://picsum.photos/200/200?random=" . rand(1, 1000);
        return view('home.attendance.org-reports.create', $data);
    }

}