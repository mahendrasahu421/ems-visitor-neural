<?php

namespace App\Services;

use App\Models\Shift;
use Carbon\Carbon;

class ShiftAttendanceService
{
    private static ?Shift $cachedDefaultShift = null;

    /**
     * Get the default fallback shift if an employee doesn't have an explicit shift.
     */
    public static function getDefaultShift(): Shift
    {
        if (self::$cachedDefaultShift === null) {
            self::$cachedDefaultShift = Shift::where('status', 'Active')->orderBy('id')->first();

            if (!self::$cachedDefaultShift) {
                self::$cachedDefaultShift = Shift::create([
                    'shift_name' => 'General Shift',
                    'shift_code' => 'GS',
                    'start_time' => '09:30:00',
                    'end_time' => '18:30:00',
                    'break_duration' => 60,
                    'total_working_hours' => 8.00,
                    'grace_period' => 15,
                    'status' => 'Active',
                ]);
            }
        }

        return self::$cachedDefaultShift;
    }

    /**
     * Format minutes to human readable string (e.g. 45m, 1h 15m)
     */
    public static function formatMinutes(int $minutes): string
    {
        if ($minutes < 60) {
            return "{$minutes}m";
        }

        $hours = intdiv($minutes, 60);
        $rem = $minutes % 60;
        return $rem > 0 ? "{$hours}h {$rem}m" : "{$hours}h";
    }

    /**
     * Evaluate punch-in and punch-out against shift timings.
     *
     * @param string|Carbon|null $checkInAt
     * @param string|Carbon|null $checkOutAt
     * @param Shift|null $shift
     * @param string|null $attendanceDate (Y-m-d)
     * @return array
     */
    public static function evaluate($checkInAt, $checkOutAt = null, ?Shift $shift = null, ?string $attendanceDate = null): array
    {
        $shift = $shift ?: self::getDefaultShift();

        $shiftName = $shift->shift_name ?? 'General Shift';
        $shiftCode = $shift->shift_code ?? 'GS';
        $startTimeStr = $shift->start_time ?? '09:30:00';
        $endTimeStr = $shift->end_time ?? '18:30:00';
        $gracePeriod = (int) ($shift->grace_period ?? 15);

        $shiftTimingFormatted = date('h:i A', strtotime($startTimeStr)) . ' - ' . date('h:i A', strtotime($endTimeStr));

        $result = [
            'shift_id' => $shift->id ?? null,
            'shift_name' => $shiftName,
            'shift_code' => $shiftCode,
            'shift_timing' => $shiftTimingFormatted,
            'grace_period' => $gracePeriod,
            'is_late' => false,
            'late_minutes' => 0,
            'late_formatted' => '',
            'late_text' => 'On Time',
            'late_badge' => '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 rounded-pill fs-11"><i class="ti ti-circle-check me-0.5"></i>On Time</span>',
            'is_early_in' => false,
            'early_in_minutes' => 0,
            'early_in_formatted' => '',
            'early_in_text' => '-',
            'early_in_badge' => '',
            'is_early_out' => false,
            'early_minutes' => 0,
            'early_formatted' => '',
            'early_text' => '-',
            'early_badge' => '<span class="badge bg-light text-muted border px-2 py-0.5 rounded-pill fs-11">-</span>',
        ];

        if (empty($checkInAt)) {
            $result['late_text'] = 'No Punch In';
            $result['late_badge'] = '<span class="badge bg-secondary-subtle text-secondary border px-2 py-0.5 rounded-pill fs-11">Absent / No Punch</span>';
            return $result;
        }

        $checkIn = Carbon::parse($checkInAt);
        $dateStr = !empty($attendanceDate) ? Carbon::parse($attendanceDate)->toDateString() : $checkIn->toDateString();

        $shiftStart = Carbon::parse($dateStr . ' ' . $startTimeStr);
        $lateCutoff = (clone $shiftStart)->addMinutes($gracePeriod);

        // Check Early In (Early Comer)
        if ($checkIn->lt($shiftStart)) {
            $earlyInMins = (int) abs(round($shiftStart->diffInMinutes($checkIn)));
            $formattedEarlyIn = self::formatMinutes($earlyInMins);
            $result['is_early_in'] = true;
            $result['early_in_minutes'] = $earlyInMins;
            $result['early_in_formatted'] = $formattedEarlyIn;
            $result['early_in_text'] = "Early In by {$formattedEarlyIn}";
            $result['early_in_badge'] = '<span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-0.5 rounded-pill fs-11 fw-semibold"><i class="ti ti-clock-play me-0.5"></i>Early In by ' . $formattedEarlyIn . '</span>';
        }

        // Check if Late
        if ($checkIn->gt($lateCutoff)) {
            $lateMinutes = (int) abs(round($checkIn->diffInMinutes($shiftStart)));
            $formattedLate = self::formatMinutes($lateMinutes);

            $result['is_late'] = true;
            $result['late_minutes'] = $lateMinutes;
            $result['late_formatted'] = $formattedLate;
            $result['late_text'] = "Late by {$formattedLate}";
            $result['late_badge'] = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 rounded-pill fs-11 fw-semibold"><i class="ti ti-clock-pause me-0.5"></i>Late by ' . $formattedLate . '</span>';
        } else {
            $result['is_late'] = false;
            $result['late_minutes'] = 0;
            $result['late_formatted'] = '';
            if ($result['is_early_in']) {
                $result['late_text'] = "Early In ({$result['early_in_formatted']})";
                $result['late_badge'] = $result['early_in_badge'];
            } else {
                $result['late_text'] = 'On Time';
                $result['late_badge'] = '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 rounded-pill fs-11 fw-semibold"><i class="ti ti-circle-check me-0.5"></i>On Time</span>';
            }
        }

        // Check Early Out
        $shiftEnd = Carbon::parse($dateStr . ' ' . $endTimeStr);
        if (!empty($checkOutAt)) {
            $checkOut = Carbon::parse($checkOutAt);

            if ($checkOut->lt($shiftEnd)) {
                $earlyMinutes = (int) abs(round($shiftEnd->diffInMinutes($checkOut)));
                $formattedEarly = self::formatMinutes($earlyMinutes);

                $result['is_early_out'] = true;
                $result['early_minutes'] = $earlyMinutes;
                $result['early_formatted'] = $formattedEarly;
                $result['early_text'] = "Early by {$formattedEarly}";
                $result['early_badge'] = '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-0.5 rounded-pill fs-11 fw-semibold"><i class="ti ti-clock-stop me-0.5"></i>Early by ' . $formattedEarly . '</span>';
            } else {
                $result['is_early_out'] = false;
                $result['early_minutes'] = 0;
                $result['early_formatted'] = '';
                $result['early_text'] = 'Full Shift Completed';
                $result['early_badge'] = '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 rounded-pill fs-11 fw-semibold"><i class="ti ti-circle-check me-0.5"></i>Shift Completed</span>';
            }
        } else {
            $result['is_early_out'] = false;
            $result['early_minutes'] = 0;
            $result['early_text'] = 'In Office (In Progress)';
            $result['early_badge'] = '<span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-0.5 rounded-pill fs-11 fw-semibold"><i class="ti ti-clock-play me-0.5"></i>In Office</span>';
        }

        return $result;
    }

    /**
     * Determine if a date is a Week-Off for a specific employee / shift.
     * Hierarchy:
     * 1. Employee-level custom week-off days ($employee->week_off_days)
     * 2. Shift-level custom week-off days ($shift->week_off_days)
     * 3. Organization Payroll / Working Days setting policy ($policy->weekly_off_pattern)
     * Fallback: Sunday
     */
    public static function isWeekOff($date, $employee = null, ?Shift $shift = null, ?\App\Models\PayrollWorkingDaysSetting $policy = null): bool
    {
        $d = is_string($date) ? Carbon::parse($date) : $date;
        $dayName = $d->format('l'); // 'Sunday', 'Monday', 'Tuesday', etc.
        $dayIndex = $d->dayOfWeek;  // 0 (Sun) - 6 (Sat)
        $dayShort = $d->format('D'); // 'Sun', 'Mon', etc.

        // 1. Employee-level week-off override
        if ($employee && !empty($employee->week_off_days)) {
            $empDays = is_array($employee->week_off_days) ? $employee->week_off_days : json_decode($employee->week_off_days, true);
            if (is_array($empDays) && count($empDays) > 0) {
                return self::matchesWeekOffDay($empDays, $dayName, $dayShort, $dayIndex);
            }
        }

        // 2. Shift-level week-off override
        $empShift = $shift ?: ($employee?->shift ?: self::getDefaultShift());
        if ($empShift && !empty($empShift->week_off_days)) {
            $shiftDays = is_array($empShift->week_off_days) ? $empShift->week_off_days : json_decode($empShift->week_off_days, true);
            if (is_array($shiftDays) && count($shiftDays) > 0) {
                return self::matchesWeekOffDay($shiftDays, $dayName, $dayShort, $dayIndex);
            }
        }

        // 3. Organization Policy (PayrollWorkingDaysSetting)
        $activePolicy = $policy ?: \App\Models\PayrollWorkingDaysSetting::getActiveSetting();
        if ($activePolicy) {
            if (!empty($activePolicy->custom_week_off_days)) {
                $polDays = is_array($activePolicy->custom_week_off_days) ? $activePolicy->custom_week_off_days : json_decode($activePolicy->custom_week_off_days, true);
                if (is_array($polDays) && count($polDays) > 0) {
                    return self::matchesWeekOffDay($polDays, $dayName, $dayShort, $dayIndex);
                }
            }

            $pattern = $activePolicy->weekly_off_pattern ?? 'sunday_only';
            if ($pattern === 'sunday_only') {
                return $d->isSunday();
            } elseif ($pattern === 'saturday_sunday') {
                return $d->isWeekend();
            } elseif ($pattern === 'alternate_saturday_sunday') {
                if ($d->isSunday()) return true;
                if ($d->isSaturday()) {
                    $satNumber = (int) ceil($d->day / 7);
                    return in_array($satNumber, [2, 4], true);
                }
                return false;
            }
        }

        return $d->isSunday();
    }

    private static function matchesWeekOffDay(array $configuredDays, string $dayName, string $dayShort, int $dayIndex): bool
    {
        $normalized = array_map(fn($v) => strtolower(trim((string)$v)), $configuredDays);
        return in_array(strtolower($dayName), $normalized, true)
            || in_array(strtolower($dayShort), $normalized, true)
            || in_array((string)$dayIndex, $normalized, true);
    }
}
