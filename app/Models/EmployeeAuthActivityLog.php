<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAuthActivityLog extends Model
{
    protected $table = 'employee_auth_activity_logs';
    public $timestamps = false;

    protected $fillable = [
        'employee_id',
        'action',
        'status',
        'ip_address',
        'user_agent',
        'device_name',
        'details',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'details' => 'array',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public static function record($employeeId, string $action, string $status = 'success', ?string $ip = null, ?string $userAgent = null, ?string $deviceName = null, $details = null)
    {
        try {
            return self::create([
                'employee_id' => $employeeId,
                'action' => $action,
                'status' => $status,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'device_name' => $deviceName,
                'details' => is_array($details) ? json_encode($details) : $details,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Failsafe so logging never breaks auth flow
            \Log::error('Auth activity log failed: ' . $e->getMessage());
            return null;
        }
    }
}
