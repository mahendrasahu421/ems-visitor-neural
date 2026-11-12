<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class EmployeeAttendanceController extends Controller
{
    public function toggle(Request $request)
    {
        $data = $request->validate([
            'login' => 'required_without:employee_id|string',
            'employee_id' => 'required_without:login|integer',
            'password' => 'required|string',
            'action' => 'nullable|in:checkin,checkout,auto',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'note' => 'nullable|string|max:1000',
        ]);

        $employee = $this->findEmployee($data);

        if (!$employee || !Hash::check($data['password'], $employee->employee_password)) {
            throw ValidationException::withMessages([
                'login' => ['Invalid employee credentials.'],
            ]);
        }

        if (!$employee->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Employee account is inactive. You cannot mark attendance.',
            ], 403);
        }

       $now = Carbon::now('Asia/Kolkata');
        $attendance = EmployeeAttendance::firstOrNew([
            'employee_id' => $employee->employee_id,
            'attendance_date' => $now->toDateString(),
        ]);

        $action = $data['action'] ?? 'auto';
        if ($action === 'auto') {
            $action = $attendance->exists && $attendance->check_in_at && !$attendance->check_out_at
                ? 'checkout'
                : 'checkin';
        }

        if ($action === 'checkin') {
            if ($attendance->exists && $attendance->check_in_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Check-in already marked for today.',
                    'data' => $this->attendancePayload($attendance),
                ], 409);
            }

            $attendance->fill([
                'check_in_at' => $now,
                'check_in_latitude' => $data['latitude'] ?? null,
                'check_in_longitude' => $data['longitude'] ?? null,
                'check_in_ip' => $request->ip(),
                'check_in_user_agent' => $request->userAgent(),
                'status' => 'checked_in',
                'note' => $data['note'] ?? null,
            ]);
        }

        if ($action === 'checkout') {
            if (!$attendance->exists || !$attendance->check_in_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please check in before check-out.',
                ], 422);
            }

            if ($attendance->check_out_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Check-out already marked for today.',
                    'data' => $this->attendancePayload($attendance),
                ], 409);
            }

            $attendance->fill([
                'check_out_at' => $now,
                'check_out_latitude' => $data['latitude'] ?? null,
                'check_out_longitude' => $data['longitude'] ?? null,
                'check_out_ip' => $request->ip(),
                'check_out_user_agent' => $request->userAgent(),
                'status' => 'checked_out',
                'note' => $data['note'] ?? $attendance->note,
            ]);
        }

        $attendance->save();

        return response()->json([
            'success' => true,
            'message' => $action === 'checkin' ? 'Check-in marked successfully.' : 'Check-out marked successfully.',
            'data' => $this->attendancePayload($attendance->fresh('employee')),
        ]);
    }

    private function findEmployee(array $data): ?Employee
    {
        if (!empty($data['employee_id'])) {
            return Employee::where('employee_id', $data['employee_id'])->first();
        }

        return Employee::where('employee_email', $data['login'])
            ->orWhere('employee_mobile', $data['login'])
            ->orWhere('employee_name', $data['login'])
            ->first();
    }

    private function attendancePayload(EmployeeAttendance $attendance): array
    {
        $checkIn = $attendance->check_in_at;
        $checkOut = $attendance->check_out_at;

        return [
            'attendance_id' => $attendance->id,
            'employee_id' => $attendance->employee_id,
            'employee_name' => $attendance->employee?->employee_name,
            'attendance_date' => optional($attendance->attendance_date)->toDateString(),
            'check_in_at' => optional($checkIn)->toDateTimeString(),
            'check_out_at' => optional($checkOut)->toDateTimeString(),
            'working_minutes' => ($checkIn && $checkOut) ? $checkIn->diffInMinutes($checkOut) : null,
            'status' => $attendance->status,
        ];
    }
}
