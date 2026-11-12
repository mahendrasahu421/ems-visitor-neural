<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAttendance;
use App\Services\FirebaseNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmployeeAttendanceApiController extends Controller
{
    /**
     * Mark Attendance Check-In.
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'note' => 'nullable|string|max:1000',
            'device_info' => 'nullable|string|max:255',
        ]);

        $employee = $request->user();
        if (!$employee->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Your employee account is currently inactive. You cannot mark check-in.',
            ], 403);
        }
        $now = Carbon::now('Asia/Kolkata');
        $todayDate = $now->toDateString();

        $attendance = EmployeeAttendance::firstOrNew([
            'employee_id' => $employee->employee_id,
            'attendance_date' => $todayDate,
        ]);

        if ($attendance->exists && $attendance->check_in_at) {
            return response()->json([
                'success' => false,
                'message' => 'Check-in is already marked for today at ' . $attendance->check_in_at->format('h:i A') . '.',
                'data' => $this->formatAttendance($attendance),
            ], 409);
        }

        $userAgent = $request->input('device_info', $request->userAgent());

        $attendance->fill([
            'check_in_at' => $now,
            'check_in_latitude' => $request->input('latitude'),
            'check_in_longitude' => $request->input('longitude'),
            'check_in_ip' => $request->ip(),
            'check_in_user_agent' => $userAgent,
            'status' => 'checked_in',
            'note' => $request->input('note'),
        ]);

        $attendance->save();

        // Push Notification to Supervisor
        if (!empty($employee->supervisor_id)) {
            try {
                app(FirebaseNotificationService::class)->sendToEmployee(
                    (int) $employee->supervisor_id,
                    'Employee Check-In',
                    "{$employee->employee_name} checked in at " . $now->format('h:i A') . '.',
                    [
                        'type' => 'attendance_checkin',
                        'employee_id' => $employee->employee_id,
                        'attendance_id' => $attendance->id,
                        'time' => $now->toIso8601String(),
                    ],
                    [
                        'sender_type' => 'employee',
                        'sender_id' => $employee->employee_id,
                    ]
                );
            } catch (\Throwable $e) {
                // Non-blocking
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Checked in successfully at ' . $now->format('h:i A') . '.',
            'data' => $this->formatAttendance($attendance),
        ], 200);
    }

    /**
     * Mark Attendance Check-Out.
     */
    public function checkOut(Request $request)
    {
        $request->validate([
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'note' => 'nullable|string|max:1000',
            'device_info' => 'nullable|string|max:255',
        ]);

        $employee = $request->user();
        if (!$employee->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Your employee account is currently inactive. You cannot mark check-out.',
            ], 403);
        }
        $now = Carbon::now('Asia/Kolkata');
        $todayDate = $now->toDateString();

        $attendance = EmployeeAttendance::where('employee_id', $employee->employee_id)
            ->where('attendance_date', $todayDate)
            ->first();

        if (!$attendance || !$attendance->check_in_at) {
            return response()->json([
                'success' => false,
                'message' => 'No check-in record found for today. Please check in first before checking out.',
            ], 422);
        }

        if ($attendance->check_out_at) {
            return response()->json([
                'success' => false,
                'message' => 'Check-out is already completed for today at ' . $attendance->check_out_at->format('h:i A') . '.',
                'data' => $this->formatAttendance($attendance),
            ], 409);
        }

        $userAgent = $request->input('device_info', $request->userAgent());

        $attendance->fill([
            'check_out_at' => $now,
            'check_out_latitude' => $request->input('latitude'),
            'check_out_longitude' => $request->input('longitude'),
            'check_out_ip' => $request->ip(),
            'check_out_user_agent' => $userAgent,
            'status' => 'checked_out',
            'note' => $request->filled('note') ? $request->input('note') : $attendance->note,
        ]);

        $attendance->save();

        // Push Notification to Supervisor
        if (!empty($employee->supervisor_id)) {
            try {
                app(FirebaseNotificationService::class)->sendToEmployee(
                    (int) $employee->supervisor_id,
                    'Employee Check-Out',
                    "{$employee->employee_name} checked out at " . $now->format('h:i A') . '.',
                    [
                        'type' => 'attendance_checkout',
                        'employee_id' => $employee->employee_id,
                        'attendance_id' => $attendance->id,
                        'time' => $now->toIso8601String(),
                    ],
                    [
                        'sender_type' => 'employee',
                        'sender_id' => $employee->employee_id,
                    ]
                );
            } catch (\Throwable $e) {
                // Non-blocking
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Checked out successfully at ' . $now->format('h:i A') . '. Shift completed.',
            'data' => $this->formatAttendance($attendance),
        ], 200);
    }

    /**
     * Get Today's Attendance Status and Live Work Timer.
     */
    public function today(Request $request)
    {
        $employee = $request->user();
        $now = Carbon::now('Asia/Kolkata');
        $todayDate = $now->toDateString();

        $attendance = EmployeeAttendance::where('employee_id', $employee->employee_id)
            ->where('attendance_date', $todayDate)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => true,
                'data' => [
                    'date' => $todayDate,
                    'is_checked_in' => false,
                    'is_checked_out' => false,
                    'timer_active' => false,
                    'status' => 'not_marked',
                    'check_in_at' => null,
                    'check_in_time' => null,
                    'check_out_at' => null,
                    'check_out_time' => null,
                    'working_seconds' => 0,
                    'working_minutes' => 0,
                    'working_hours' => 0,
                    'working_hours_formatted' => '0h 0m 0s',
                    'working_time_clock' => '00:00:00',
                    'timer' => [
                        'is_running' => false,
                        'status' => 'not_started',
                        'check_in_time' => null,
                        'check_in_time_short' => null,
                        'check_in_timestamp' => null,
                        'check_in_timestamp_ms' => null,
                        'check_out_time' => null,
                        'check_out_time_short' => null,
                        'check_out_timestamp' => null,
                        'check_out_timestamp_ms' => null,
                        'server_time' => $now->toIso8601String(),
                        'server_timestamp_ms' => $now->timestamp * 1000,
                        'elapsed_seconds' => 0,
                        'elapsed_minutes' => 0,
                        'elapsed_hours' => 0,
                        'clock' => '00:00:00',
                        'formatted' => '0h 0m 0s',
                    ],
                ],
            ], 200);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatAttendance($attendance),
        ], 200);
    }

    /**
     * Get Attendance History with filters.
     */
    public function history(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
            'status' => 'nullable|string|in:checked_in,checked_out,present,absent,half_day,late',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $employee = $request->user();
        $query = EmployeeAttendance::where('employee_id', $employee->employee_id);

        if ($request->filled('start_date')) {
            $query->whereDate('attendance_date', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('attendance_date', '<=', $request->input('end_date'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $perPage = (int) $request->input('per_page', 20);
        $attendances = $query->orderBy('attendance_date', 'desc')->paginate($perPage);

        $records = $attendances->map(function ($att) {
            return $this->formatAttendance($att);
        });

        // Summary metrics for current query range
        $now = Carbon::now('Asia/Kolkata');
        $totalWorkedSeconds = $attendances->sum(function ($att) use ($now) {
            if ($att->check_in_at && $att->check_out_at) {
                return max(0, $att->check_in_at->diffInSeconds($att->check_out_at));
            } elseif ($att->check_in_at) {
                return max(0, $att->check_in_at->diffInSeconds($now));
            }
            return 0;
        });

        $totalMinutes = floor($totalWorkedSeconds / 60);

        return response()->json([
            'success' => true,
            'data' => [
                'records' => $records,
                'pagination' => [
                    'current_page' => $attendances->currentPage(),
                    'last_page' => $attendances->lastPage(),
                    'per_page' => $attendances->perPage(),
                    'total_records' => $attendances->total(),
                ],
                'summary' => [
                    'total_working_seconds' => $totalWorkedSeconds,
                    'total_working_minutes' => $totalMinutes,
                    'total_working_hours' => round($totalWorkedSeconds / 3600, 2),
                ],
            ],
        ], 200);
    }

    /**
     * Format an Attendance record with live work timer metrics.
     */
    private function formatAttendance(EmployeeAttendance $attendance): array
    {
        $checkIn = $attendance->getCheckInIST();
        $checkOut = $attendance->getCheckOutIST();
        $timer = $attendance->getTimerPayload();

        return [
            'id' => $attendance->id,
            'employee_id' => $attendance->employee_id,
            'date' => optional($attendance->attendance_date)->toDateString(),
            'check_in_at' => optional($checkIn)->toDateTimeString(),
            'check_in_time' => optional($checkIn)->format('h:i A'),
            'check_out_at' => optional($checkOut)->toDateTimeString(),
            'check_out_time' => optional($checkOut)->format('h:i A'),
            'is_checked_in' => (bool) $checkIn,
            'is_checked_out' => (bool) $checkOut,
            'timer_active' => $attendance->is_timer_running,
            'status' => $attendance->status,
            'working_seconds' => $attendance->worked_seconds,
            'working_minutes' => (int) floor($attendance->worked_seconds / 60),
            'working_hours' => round($attendance->worked_seconds / 3600, 2),
            'working_hours_formatted' => $attendance->worked_formatted,
            'working_time_clock' => $attendance->worked_clock,
            'timer' => $timer,
            // 'check_in_location' => [
            //     'latitude' => $attendance->check_in_latitude,
            //     'longitude' => $attendance->check_in_longitude,
            //     'ip' => $attendance->check_in_ip,
            // ],
            // 'check_out_location' => [
            //     'latitude' => $attendance->check_out_latitude,
            //     'longitude' => $attendance->check_out_longitude,
            //     'ip' => $attendance->check_out_ip,
            // ],
            'note' => $attendance->note,
        ];
    }
}
