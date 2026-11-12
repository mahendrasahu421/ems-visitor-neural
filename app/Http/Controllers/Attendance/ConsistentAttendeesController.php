<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ConsistentAttendeesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $selectedMonth = (int) $request->input('month', Carbon::now()->month);
        $selectedYear = (int) $request->input('year', Carbon::now()->year);
        $selectedDept = $request->input('department_id', '');
        $staffStatus = $request->input('staff_status', 'active');
        $tierFilter = $request->input('tier', 'all'); // 'all', 'top' (>=90%), 'good' (75-89%), 'low' (<75%)
        $search = trim((string) $request->input('search', ''));

        $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth();
        $endDate = (clone $startDate)->endOfMonth();

        // If current month & year, limit working days count up to today so current month percentage is accurate
        $isCurrentMonth = ($selectedYear == Carbon::now()->year && $selectedMonth == Carbon::now()->month);
        $effectiveEndDate = $isCurrentMonth ? Carbon::today() : $endDate;

        // Calculate total working days in period (excluding Sundays and holidays)
        $holidays = Holiday::whereBetween('holiday_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->pluck('holiday_date')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->toArray();

        $workingDays = 0;
        $currentDay = clone $startDate;
        while ($currentDay->lte($effectiveEndDate)) {
            // Not Sunday and not Holiday
            if ($currentDay->dayOfWeek !== Carbon::SUNDAY && !in_array($currentDay->toDateString(), $holidays)) {
                $workingDays++;
            }
            $currentDay->addDay();
        }
        $totalWorkingDays = max($workingDays, 1);

        // Query employees
        $empQuery = Employee::with(['designation', 'supervisor']);
        if ($staffStatus === 'active') {
            $empQuery->whereNull('deleted_at')
                ->where(function ($q) {
                    $q->where('status', 'Active')
                        ->orWhere('status', 1)
                        ->orWhere('status', '1')
                        ->orWhere('status', 'active')
                        ->orWhere('status', '')
                        ->orWhereNull('status');
                });
        } elseif ($staffStatus === 'inactive') {
            $empQuery->where(function ($q) {
                $q->where('status', 'Inactive')
                    ->orWhere('status', 0)
                    ->orWhere('status', '0')
                    ->orWhere('status', 'inactive')
                    ->orWhereNotNull('deleted_at');
            });
        }

        if (!empty($search)) {
            $empQuery->where(function ($q) use ($search) {
                $q->where('employee_name', 'like', "%{$search}%")
                    ->orWhere('employee_email', 'like', "%{$search}%")
                    ->orWhere('employee_id', $search);
            });
        }

        $employees = $empQuery->get();

        // Fetch attendances in this month
        $attendances = EmployeeAttendance::whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->whereIn('employee_id', $employees->pluck('employee_id'))
            ->get()
            ->groupBy('employee_id');

        $attendeesList = [];
        $topCount = 0;      // >= 90%
        $goodCount = 0;     // 75% - 89%
        $lowCount = 0;      // < 75%
        $totalConsistencySum = 0;
        $maxOrgStreak = 0;

        foreach ($employees as $emp) {
            $empAtts = $attendances->get($emp->employee_id, collect());
            $presentDays = $empAtts->count();

            // Calculate consistency %
            $consistencyPct = round(min(100, ($presentDays / $totalWorkingDays) * 100), 1);
            $totalConsistencySum += $consistencyPct;

            // Calculate consecutive present days streak
            $dates = $empAtts->pluck('attendance_date')
                ->map(fn($d) => Carbon::parse($d)->toDateString())
                ->unique()
                ->sort()
                ->values();

            $maxStreak = 0;
            $currentStreak = 0;
            $prevDate = null;
            foreach ($dates as $dStr) {
                $d = Carbon::parse($dStr);
                if ($prevDate) {
                    $diff = $prevDate->diffInDays($d);
                    if ($diff == 1 || ($diff == 2 && $prevDate->dayOfWeek == Carbon::SATURDAY)) {
                        $currentStreak++;
                    } else {
                        $currentStreak = 1;
                    }
                } else {
                    $currentStreak = 1;
                }
                if ($currentStreak > $maxStreak) {
                    $maxStreak = $currentStreak;
                }
                $prevDate = $d;
            }

            if ($maxStreak > $maxOrgStreak) {
                $maxOrgStreak = $maxStreak;
            }

            // Calculate Average Daily Hours
            $totalMinutes = 0;
            $validCheckoutCount = 0;
            foreach ($empAtts as $att) {
                if ($att->check_in_at && $att->check_out_at) {
                    $in = Carbon::parse($att->check_in_at);
                    $out = Carbon::parse($att->check_out_at);
                    if ($out->gt($in)) {
                        $totalMinutes += $in->diffInMinutes($out);
                        $validCheckoutCount++;
                    }
                }
            }
            $avgMinutes = $validCheckoutCount > 0 ? round($totalMinutes / $validCheckoutCount) : 0;
            $avgHoursFormatted = $avgMinutes > 0 ? floor($avgMinutes / 60) . 'h ' . ($avgMinutes % 60) . 'm' : '—';

            // Categorize Tier
            if ($consistencyPct >= 90) {
                $tier = 'top';
                $tierLabel = 'Consistent Star';
                $tierBadge = 'bg-success-subtle text-success border border-success-subtle';
                $topCount++;
            } elseif ($consistencyPct >= 75) {
                $tier = 'good';
                $tierLabel = 'Moderately Regular';
                $tierBadge = 'bg-primary-subtle text-primary border border-primary-subtle';
                $goodCount++;
            } else {
                $tier = 'low';
                $tierLabel = 'Needs Attention';
                $tierBadge = 'bg-danger-subtle text-danger border border-danger-subtle';
                $lowCount++;
            }

            // Check tier filter
            if ($tierFilter !== 'all' && $tier !== $tierFilter) {
                continue;
            }

            $attendeesList[] = [
                'employee' => $emp,
                'present_days' => $presentDays,
                'working_days' => $totalWorkingDays,
                'consistency_pct' => $consistencyPct,
                'streak' => $maxStreak,
                'avg_hours' => $avgHoursFormatted,
                'tier' => $tier,
                'tier_label' => $tierLabel,
                'tier_badge' => $tierBadge,
            ];
        }

        // Sort leaderboard by consistency_pct DESC, then present_days DESC, then streak DESC
        usort($attendeesList, function ($a, $b) {
            if ($a['consistency_pct'] == $b['consistency_pct']) {
                if ($a['present_days'] == $b['present_days']) {
                    return $b['streak'] <=> $a['streak'];
                }
                return $b['present_days'] <=> $a['present_days'];
            }
            return $b['consistency_pct'] <=> $a['consistency_pct'];
        });

        $totalEmpCount = $employees->count();
        $avgOrgConsistency = $totalEmpCount > 0 ? round($totalConsistencySum / $totalEmpCount, 1) : 0;

        $departments = Department::whereNull('deleted_at')->orderBy('department_name')->get();

        return view('home.attendance.daily-attendance.index', compact(
            'attendeesList',
            'selectedMonth',
            'selectedYear',
            'selectedDept',
            'staffStatus',
            'tierFilter',
            'search',
            'topCount',
            'goodCount',
            'lowCount',
            'totalEmpCount',
            'avgOrgConsistency',
            'maxOrgStreak',
            'totalWorkingDays',
            'departments'
        ))->with('title', 'Consistent Attendees & Punctuality Leaderboard');
    }
}
