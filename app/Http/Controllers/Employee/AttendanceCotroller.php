<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAttendance;
use App\Models\Shift;
use App\Services\ShiftAttendanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceCotroller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employee = Auth::guard('employee')->user();
        if (!$employee) {
            return redirect()->route('employee.login');
        }

        $now = Carbon::now('Asia/Kolkata');
        $todayDate = $now->toDateString();

        $todayAttendance = EmployeeAttendance::where('employee_id', $employee->employee_id)
            ->whereDate('attendance_date', $todayDate)
            ->first();

        $monthAttendances = EmployeeAttendance::where('employee_id', $employee->employee_id)
            ->whereMonth('attendance_date', $now->month)
            ->whereYear('attendance_date', $now->year)
            ->orderByDesc('attendance_date')
            ->get();

        $shift = $employee->shift ?: ShiftAttendanceService::getDefaultShift();

        $todayHours = 0;
        if ($todayAttendance && $todayAttendance->check_in_at) {
            $endTime = $todayAttendance->check_out_at ?: $now;
            $todayHours = round($todayAttendance->check_in_at->diffInMinutes($endTime) / 60, 2);
        }

        $data['title'] = 'My Attendance';
        $data['employee'] = $employee;
        $data['shift'] = $shift;
        $data['todayAttendance'] = $todayAttendance;
        $data['monthAttendances'] = $monthAttendances;
        $data['todayHours'] = $todayHours;
        $data['now'] = $now;

        return view('employee.attendance.index', $data);
    }

    /**
     * Handle Web Punch In / Punch Out.
     */
    public function punch(Request $request)
    {
        $employee = Auth::guard('employee')->user();
        if (!$employee) {
            return redirect()->route('employee.login');
        }

        if (strtolower((string)$employee->status) === 'inactive' || !empty($employee->deleted_at)) {
            return redirect()->back()->with('error', 'Your employee account is inactive. You cannot mark attendance.');
        }

        $now = Carbon::now('Asia/Kolkata');
        $todayDate = $now->toDateString();

        $attendance = EmployeeAttendance::firstOrNew([
            'employee_id'     => $employee->employee_id,
            'attendance_date' => $todayDate,
        ]);

        if (!$attendance->exists || !$attendance->check_in_at) {
            // Punch In
            $attendance->check_in_at = $now;
            $attendance->check_in_ip = $request->ip();
            $attendance->check_in_user_agent = $request->userAgent();
            $attendance->status = 'checked_in';
            $attendance->save();

            return redirect()->back()->with('success', 'Checked In successfully at ' . $now->format('h:i A') . '!');
        }

        if (!$attendance->check_out_at) {
            // Punch Out
            $attendance->check_out_at = $now;
            $attendance->check_out_ip = $request->ip();
            $attendance->check_out_user_agent = $request->userAgent();
            $attendance->status = 'checked_out';
            $attendance->save();

            return redirect()->back()->with('success', 'Checked Out successfully at ' . $now->format('h:i A') . '!');
        }

        return redirect()->back()->with('warning', 'You have already checked in and checked out for today.');
    }
}
