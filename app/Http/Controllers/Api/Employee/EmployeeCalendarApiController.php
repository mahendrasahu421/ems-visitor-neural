<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAttendance;
use App\Models\Holiday;
use App\Models\Leave;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class EmployeeCalendarApiController extends Controller
{
    /**
     * Get Monthly Aggregated Calendar for Employee.
     * Combines Attendance, Weekly Offs (Sundays), Public Holidays, and Approved Leaves.
     */
    public function calendar(Request $request)
    {
        $request->validate([
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer|between:2020,2035',
        ]);

        $employee = $request->user();
        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $today = Carbon::today();

        // 1. Fetch employee's attendances for the month
        $attendances = EmployeeAttendance::where('employee_id', $employee->employee_id)
            ->whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->attendance_date)->toDateString();
            });

        // 2. Fetch public/festival holidays for the month
        $holidays = Holiday::where('status', 1)
            ->whereBetween('holiday_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->holiday_date)->toDateString();
            });

        // 3. Fetch employee leaves overlapping this month
        $leaves = Leave::with('leaveType')
            ->where('employee_id', $employee->employee_id)
            ->where('status', 'APPROVED')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('from_date', [$startDate->toDateString(), $endDate->toDateString()])
                  ->orWhereBetween('to_date', [$startDate->toDateString(), $endDate->toDateString()])
                  ->orWhere(function ($sub) use ($startDate, $endDate) {
                      $sub->where('from_date', '<=', $startDate->toDateString())
                          ->where('to_date', '>=', $endDate->toDateString());
                  });
            })
            ->get();

        // Create date-to-leave mapping
        $leaveMap = [];
        foreach ($leaves as $leave) {
            $leaveStart = Carbon::parse($leave->from_date)->clamp($startDate, $endDate);
            $leaveEnd = Carbon::parse($leave->to_date)->clamp($startDate, $endDate);
            $period = CarbonPeriod::create($leaveStart, $leaveEnd);
            foreach ($period as $dt) {
                $leaveMap[$dt->toDateString()] = [
                    'leave_id' => $leave->id,
                    'leave_type' => $leave->leaveType?->name ?? 'Leave',
                    'reason' => $leave->reason,
                    'status' => $leave->status,
                ];
            }
        }

        // 4. Generate calendar day by day
        $calendarDays = [];
        $period = CarbonPeriod::create($startDate, $endDate);

        $presentCount = 0;
        $absentCount = 0;
        $leaveCount = 0;
        $holidayCount = 0;
        $weeklyOffCount = 0;

        foreach ($period as $date) {
            $dateStr = $date->toDateString();
            $dayOfWeek = $date->format('l');
            $isSunday = ($date->dayOfWeek === Carbon::SUNDAY);
            $isFuture = $date->isAfter($today);

            $att = $attendances[$dateStr] ?? null;
            $holiday = $holidays[$dateStr] ?? null;
            $leave = $leaveMap[$dateStr] ?? null;

            // Determine primary status tag & color
            $dayType = 'regular';
            $status = 'pending';
            $statusLabel = 'Pending';
            $color = '#6c757d'; // default gray

            if ($holiday) {
                $dayType = 'holiday';
                $status = 'holiday';
                $statusLabel = 'Holiday: ' . $holiday->holiday_name;
                $color = '#0dcaf0'; // info cyan
                $holidayCount++;
            } elseif ($leave) {
                $dayType = 'leave';
                $status = 'on_leave';
                $statusLabel = 'Leave: ' . $leave['leave_type'];
                $color = '#6f42c1'; // purple
                $leaveCount++;
            } elseif ($isSunday) {
                $dayType = 'weekly_off';
                $status = 'weekly_off';
                $statusLabel = 'Weekly Off (Sunday)';
                $color = '#ffc107'; // amber
                $weeklyOffCount++;
            } elseif ($att && ($att->check_in_at || $att->status === 'checked_in' || $att->status === 'checked_out' || $att->status === 'present')) {
                $dayType = 'present';
                $status = 'present';
                $statusLabel = 'Present';
                $color = '#198754'; // green
                $presentCount++;
            } elseif (!$isFuture) {
                $dayType = 'absent';
                $status = 'absent';
                $statusLabel = 'Absent';
                $color = '#dc3545'; // red
                $absentCount++;
            } else {
                $dayType = 'upcoming';
                $status = 'upcoming';
                $statusLabel = 'Upcoming';
                $color = '#e2e8f0';
            }

            $calendarDays[] = [
                'date' => $dateStr,
                'day' => $date->day,
                'day_name' => $dayOfWeek,
                'day_type' => $dayType,
                'status' => $status,
                'status_label' => $statusLabel,
                'color' => $color,
                'is_today' => $date->isToday(),
                'is_future' => $isFuture,
                'attendance' => $att ? [
                    'check_in' => optional($att->check_in_at)->format('h:i A'),
                    'check_out' => optional($att->check_out_at)->format('h:i A'),
                    'working_minutes' => ($att->check_in_at && $att->check_out_at) ? $att->check_in_at->diffInMinutes($att->check_out_at) : 0,
                ] : null,
                'holiday' => $holiday ? [
                    'name' => $holiday->holiday_name,
                    'remark' => $holiday->holiday_remark,
                ] : null,
                'leave' => $leave,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'month' => $month,
                'month_name' => $startDate->format('F'),
                'year' => $year,
                'summary' => [
                    'total_days' => $startDate->daysInMonth,
                    'present_days' => $presentCount,
                    'absent_days' => $absentCount,
                    'leave_days' => $leaveCount,
                    'holiday_days' => $holidayCount,
                    'weekly_offs' => $weeklyOffCount,
                ],
                'days' => $calendarDays,
            ],
        ], 200);
    }

    /**
     * Get Detailed Breakdown for a specific date.
     */
    public function dateDetails(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        $employee = $request->user();
        $date = Carbon::parse($request->input('date'));
        $dateStr = $date->toDateString();

        $att = EmployeeAttendance::where('employee_id', $employee->employee_id)
            ->where('attendance_date', $dateStr)
            ->first();

        $holiday = Holiday::where('status', 1)
            ->where('holiday_date', $dateStr)
            ->first();

        $leave = Leave::with('leaveType')
            ->where('employee_id', $employee->employee_id)
            ->where('status', 'APPROVED')
            ->where('from_date', '<=', $dateStr)
            ->where('to_date', '>=', $dateStr)
            ->first();

        $isSunday = ($date->dayOfWeek === Carbon::SUNDAY);

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $dateStr,
                'day' => $date->format('l'),
                'is_sunday' => $isSunday,
                'is_holiday' => (bool) $holiday,
                'holiday_details' => $holiday ? [
                    'name' => $holiday->holiday_name,
                    'remark' => $holiday->holiday_remark,
                ] : null,
                'is_leave' => (bool) $leave,
                'leave_details' => $leave ? [
                    'id' => $leave->id,
                    'leave_type' => $leave->leaveType?->name,
                    'reason' => $leave->reason,
                    'duration' => $leave->duration,
                ] : null,
                'attendance_details' => $att ? [
                    'check_in_at' => optional($att->check_in_at)->toDateTimeString(),
                    'check_out_at' => optional($att->check_out_at)->toDateTimeString(),
                    'status' => $att->status,
                    'working_minutes' => ($att->check_in_at && $att->check_out_at) ? $att->check_in_at->diffInMinutes($att->check_out_at) : 0,
                    'check_in_ip' => $att->check_in_ip,
                    'check_in_location' => [
                        'latitude' => $att->check_in_latitude,
                        'longitude' => $att->check_in_longitude,
                    ],
                    'note' => $att->note,
                ] : null,
            ],
        ], 200);
    }
}
