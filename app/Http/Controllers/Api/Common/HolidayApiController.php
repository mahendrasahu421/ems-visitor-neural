<?php

namespace App\Http\Controllers\Api\Common;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HolidayApiController extends Controller
{
    /**
     * Get Holiday List for specified year.
     */
    public function index(Request $request)
    {
        $year = (int) $request->input('year', Carbon::now()->year);

        $holidays = Holiday::where('status', 1)
            ->whereYear('holiday_date', $year)
            ->orderBy('holiday_date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'year' => $year,
                'total_holidays' => $holidays->count(),
                'holidays' => $holidays->map(function ($h) {
                    $dt = Carbon::parse($h->holiday_date);
                    $isCommon = str_contains(strtolower($h->holiday_remark ?? ''), 'common');
                    return [
                        'id' => $h->id,
                        'name' => $h->holiday_name,
                        'date' => $h->holiday_date,
                        'day' => $dt->format('l'),
                        'month' => $dt->format('F'),
                        'type' => $isCommon ? 'Common' : 'Restricted',
                        'is_common' => $isCommon,
                        'remark' => $h->holiday_remark,
                        'is_past' => $dt->isPast(),
                    ];
                }),
            ],
        ], 200);
    }

    /**
     * Get Holiday Calendar grouped by month.
     */
    public function calendar(Request $request)
    {
        $year = (int) $request->input('year', Carbon::now()->year);

        $holidays = Holiday::where('status', 1)
            ->whereYear('holiday_date', $year)
            ->orderBy('holiday_date', 'asc')
            ->get();

        $grouped = $holidays->groupBy(function ($h) {
            return Carbon::parse($h->holiday_date)->format('F');
        });

        return response()->json([
            'success' => true,
            'data' => [
                'year' => $year,
                'total_holidays' => $holidays->count(),
                'months' => $grouped->map(function ($items, $monthName) {
                    return [
                        'month' => $monthName,
                        'count' => $items->count(),
                        'holidays' => $items->map(function ($h) {
                            $dt = Carbon::parse($h->holiday_date);
                            $isCommon = str_contains(strtolower($h->holiday_remark ?? ''), 'common');
                            return [
                                'id' => $h->id,
                                'name' => $h->holiday_name,
                                'date' => $h->holiday_date,
                                'day' => $dt->format('l'),
                                'type' => $isCommon ? 'Common' : 'Restricted',
                                'is_common' => $isCommon,
                                'remark' => $h->holiday_remark,
                            ];
                        })->values(),
                    ];
                }),
            ],
        ], 200);
    }

    /**
     * Get list of Restricted Holidays specifically, with employee application status if authenticated.
     */
    public function restricted(Request $request)
    {
        $year = (int) $request->input('year', Carbon::now()->year);
        $employee = $request->user();

        $holidays = Holiday::where('status', 1)
            ->whereYear('holiday_date', $year)
            ->where(function ($q) {
                $q->where('holiday_remark', 'like', '%restricted%')
                  ->orWhere('holiday_remark', 'like', '%optional%');
            })
            ->where('holiday_remark', 'not like', '%common%')
            ->orderBy('holiday_date', 'asc')
            ->get();

        $rhLeaveType = \App\Models\LeaveType::where('leave_name', 'like', '%Restricted%')
            ->orWhere('leave_code', 'RH-001')
            ->first();

        $totalAllowed = $rhLeaveType ? (int) $rhLeaveType->total_leaves : 5;
        $employeeLeaves = collect();

        if ($employee) {
            $employeeLeaves = \App\Models\Leave::where('employee_id', $employee->employee_id)
                ->where(function ($q) use ($year) {
                    $q->whereYear('from_date', $year)->orWhereYear('to_date', $year);
                })
                ->where(function ($q) use ($rhLeaveType) {
                    if ($rhLeaveType) {
                        $q->where('leave_type_id', $rhLeaveType->id);
                    }
                    $q->orWhereNotNull('holiday_id');
                })
                ->get()
                ->keyBy('holiday_id');
        }

        $usedCount = $employeeLeaves->where('status', 'APPROVED')->count();
        $pendingCount = $employeeLeaves->whereIn('status', ['DRAFT', 'SENT', 'PENDING'])->count();
        $remainingCount = max($totalAllowed - $usedCount, 0);

        $items = $holidays->map(function ($h) use ($employeeLeaves, $remainingCount) {
            $dt = Carbon::parse($h->holiday_date);
            $leave = $employeeLeaves->get($h->id);
            $hasActiveLeave = $leave && in_array(strtoupper($leave->status), ['DRAFT', 'SENT', 'PENDING', 'APPROVED']);

            return [
                'id' => $h->id,
                'name' => $h->holiday_name,
                'date' => $h->holiday_date,
                'day' => $dt->format('l'),
                'month' => $dt->format('F'),
                'remark' => $h->holiday_remark,
                'is_past' => $dt->isPast(),
                'application_status' => [
                    'applied' => (bool) $leave,
                    'leave_id' => $leave?->id,
                    'status' => $leave?->status,
                    'can_apply' => !$dt->isPast() && !$hasActiveLeave && $remainingCount > 0,
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'year' => $year,
                'quota' => [
                    'total_allowed' => $totalAllowed,
                    'used' => $usedCount,
                    'pending' => $pendingCount,
                    'remaining' => $remainingCount,
                ],
                'total_restricted_holidays' => $items->count(),
                'restricted_holidays' => $items,
            ],
        ], 200);
    }
}
