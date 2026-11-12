<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FcmDeviceToken extends Model
{
    use HasFactory;

    protected $table = 'fcm_device_tokens';

    protected $fillable = [
        'employee_id',
        'user_id',
        'token',
        'token_hash',
        'device_type',
        'device_name',
        'app_version',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    /**
     * Store or update device token.
     */
    public static function storeToken(string $token, ?int $employeeId = null, ?int $userId = null, string $deviceType = 'android', ?string $deviceName = null, ?string $appVersion = null): self
    {
        $hash = hash('sha256', $token);

        return static::updateOrCreate(
            ['token_hash' => $hash],
            [
                'employee_id' => $employeeId,
                'user_id' => $userId,
                'token' => $token,
                'device_type' => in_array($deviceType, ['android', 'ios', 'web']) ? $deviceType : 'android',
                'device_name' => $deviceName,
                'app_version' => $appVersion,
                'is_active' => true,
                'last_used_at' => now(),
            ]
        );
    }

    /**
     * Deactivate / delete device token on logout.
     */
    public static function removeToken(string $token): bool
    {
        $hash = hash('sha256', $token);
        return (bool) static::where('token_hash', $hash)->update([
            'is_active' => false,
        ]);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
