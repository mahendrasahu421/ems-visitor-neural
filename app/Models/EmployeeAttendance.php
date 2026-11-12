<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAttendance extends Model
{
    protected $fillable = [
        'employee_id',
        'attendance_date',
        'check_in_at',
        'check_out_at',
        'check_in_latitude',
        'check_in_longitude',
        'check_out_latitude',
        'check_out_longitude',
        'check_in_ip',
        'check_out_ip',
        'check_in_user_agent',
        'check_out_user_agent',
        'status',
        'is_regularized',
        'regularized_by',
        'regularization_reason',
        'note',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
        'is_regularized' => 'boolean',
        'regularized_by' => 'integer',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function regularizer()
    {
        return $this->belongsTo(Employee::class, 'regularized_by', 'employee_id');
    }

    /**
     * Get Check-In time resolved in Asia/Kolkata timezone.
     */
    public function getCheckInIST(): ?\Carbon\Carbon
    {
        if (!$this->check_in_at) {
            return null;
        }
        $raw = $this->getRawOriginal('check_in_at') ?: $this->check_in_at->format('Y-m-d H:i:s');
        return \Carbon\Carbon::parse(substr((string) $raw, 0, 19), 'Asia/Kolkata');
    }

    /**
     * Get Check-Out time resolved in Asia/Kolkata timezone.
     */
    public function getCheckOutIST(): ?\Carbon\Carbon
    {
        if (!$this->check_out_at) {
            return null;
        }
        $raw = $this->getRawOriginal('check_out_at') ?: $this->check_out_at->format('Y-m-d H:i:s');
        return \Carbon\Carbon::parse(substr((string) $raw, 0, 19), 'Asia/Kolkata');
    }

    /**
     * Get worked seconds (live timer if still checked in, or duration if checked out).
     */
    public function getWorkedSecondsAttribute(): int
    {
        $checkIn = $this->getCheckInIST();
        if (!$checkIn) {
            return 0;
        }

        $end = $this->getCheckOutIST() ?? \Carbon\Carbon::now('Asia/Kolkata');
        return max(0, $checkIn->diffInSeconds($end));
    }

    /**
     * Check if live work timer is currently running.
     */
    public function getIsTimerRunningAttribute(): bool
    {
        return (bool) ($this->check_in_at && !$this->check_out_at);
    }

    /**
     * Get worked time in digital stopwatch format (HH:MM:SS).
     */
    public function getWorkedClockAttribute(): string
    {
        $sec = $this->worked_seconds;
        $h = floor($sec / 3600);
        $m = floor(($sec % 3600) / 60);
        $s = $sec % 60;

        return sprintf('%02d:%02d:%02d', $h, $m, $s);
    }

    /**
     * Get worked time in human readable format (Xh Ym Zs).
     */
    public function getWorkedFormattedAttribute(): string
    {
        $sec = $this->worked_seconds;
        $h = floor($sec / 3600);
        $m = floor(($sec % 3600) / 60);
        $s = $sec % 60;

        return "{$h}h {$m}m {$s}s";
    }

    /**
     * Get structured timer payload for API responses.
     */
    public function getTimerPayload(): array
    {
        $checkIn = $this->getCheckInIST();
        $checkOut = $this->getCheckOutIST();
        $now = \Carbon\Carbon::now('Asia/Kolkata');
        $isTimerActive = (bool) ($checkIn && !$checkOut);
        $elapsedSeconds = $this->worked_seconds;

        $workingMinutes = floor($elapsedSeconds / 60);
        $hours = floor($elapsedSeconds / 3600);
        $mins = floor(($elapsedSeconds % 3600) / 60);
        $secs = $elapsedSeconds % 60;

        $clockFormat = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
        $humanFormat = "{$hours}h {$mins}m {$secs}s";

        return [
            'is_running' => $isTimerActive,
            'status' => $isTimerActive ? 'running' : ($checkOut ? 'completed' : 'not_started'),
            'check_in_time' => optional($checkIn)?->format('h:i:s A'),
            'check_in_time_short' => optional($checkIn)?->format('h:i A'),
            'check_in_timestamp' => optional($checkIn)?->timestamp,
            'check_in_timestamp_ms' => optional($checkIn)?->timestamp ? $checkIn->timestamp * 1000 : null,
            'check_out_time' => optional($checkOut)?->format('h:i:s A'),
            'check_out_time_short' => optional($checkOut)?->format('h:i A'),
            'check_out_timestamp' => optional($checkOut)?->timestamp,
            'check_out_timestamp_ms' => optional($checkOut)?->timestamp ? $checkOut->timestamp * 1000 : null,
            'server_time' => $now->toIso8601String(),
            'server_timestamp_ms' => $now->timestamp * 1000,
            'elapsed_seconds' => $elapsedSeconds,
            'elapsed_minutes' => $workingMinutes,
            'elapsed_hours' => round($elapsedSeconds / 3600, 2),
            'clock' => $clockFormat,
            'formatted' => $humanFormat,
        ];
    }

    /**
     * Default timer payload when no check-in exists.
     */
    public static function defaultTimerPayload(): array
    {
        $now = \Carbon\Carbon::now('Asia/Kolkata');
        return [
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
        ];
    }
}
