<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollWorkingDaysSetting extends Model
{
    use HasFactory;

    protected $table = 'payroll_working_days_settings';

    protected $fillable = [
        'policy_name',
        'calculation_basis',
        'weekly_off_pattern',
        'custom_week_off_days',
        'deduct_unpaid_leave_only',
        'include_holidays_in_payable',
        'include_weekly_off_in_payable',
        'is_default',
        'status',
        'description',
    ];

    protected $casts = [
        'custom_week_off_days' => 'array',
        'deduct_unpaid_leave_only' => 'boolean',
        'include_holidays_in_payable' => 'boolean',
        'include_weekly_off_in_payable' => 'boolean',
        'is_default' => 'boolean',
    ];

    /**
     * Get the active default working days calculation policy.
     */
    public static function getActiveSetting(): self
    {
        $setting = self::where('status', 'Active')
            ->where('is_default', true)
            ->first();

        if (!$setting) {
            $setting = self::where('status', 'Active')->first();
        }

        if (!$setting) {
            // Return an in-memory fallback default (Standard 26 days)
            $setting = new self([
                'policy_name' => 'Standard 26 Working Days (Fixed)',
                'calculation_basis' => 'fixed_26_days',
                'weekly_off_pattern' => 'sunday_only',
                'deduct_unpaid_leave_only' => true,
                'include_holidays_in_payable' => true,
                'include_weekly_off_in_payable' => true,
                'is_default' => true,
                'status' => 'Active',
            ]);
        }

        return $setting;
    }

    /**
     * Human readable calculation basis label.
     */
    public function getBasisLabelAttribute(): string
    {
        return match ($this->calculation_basis) {
            'fixed_26_days' => 'Fixed 26 Days Basis (Divisor = 26)',
            'calendar_days' => 'Calendar Days Basis (Divisor = Month Days)',
            'fixed_30_days' => 'Fixed 30 Days Basis (Divisor = 30)',
            'actual_working_days' => 'Actual Working Days (Month Days - Weekends - Holidays)',
            default => 'Standard Basis',
        };
    }

    /**
     * Human readable weekly off pattern label.
     */
    public function getWeeklyOffLabelAttribute(): string
    {
        return match ($this->weekly_off_pattern) {
            'sunday_only' => '6 Days Week (Sunday Off)',
            'saturday_sunday' => '5 Days Week (Sat & Sun Off)',
            'alternate_saturday_sunday' => 'Alternate Weekends (Sunday + 2nd/4th Saturday Off)',
            default => 'Sunday Only',
        };
    }
}
