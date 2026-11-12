<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('payroll_working_days_settings')) {
            Schema::create('payroll_working_days_settings', function (Blueprint $table) {
                $table->id();
                $table->string('policy_name', 100);
                $table->enum('calculation_basis', [
                    'fixed_26_days',         // Standard Indian 26 Days Basis (Daily = Gross / 26)
                    'calendar_days',         // Actual calendar days in month: 28/30/31 (Daily = Gross / MonthDays)
                    'fixed_30_days',         // Standard 30 Days Basis (Daily = Gross / 30)
                    'actual_working_days'    // Month Days minus weekly offs minus public holidays
                ])->default('fixed_26_days');
                
                $table->enum('weekly_off_pattern', [
                    'sunday_only',               // 6 Days week: Sundays off
                    'saturday_sunday',           // 5 Days week: Saturdays & Sundays off
                    'alternate_saturday_sunday'  // Sundays + 2nd & 4th Saturday off
                ])->default('sunday_only');

                $table->boolean('deduct_unpaid_leave_only')->default(true); // true = only LWP/Absent is deducted, Paid Leaves (CL/SL/EL) are not deducted
                $table->boolean('include_holidays_in_payable')->default(true);
                $table->boolean('include_weekly_off_in_payable')->default(true);
                $table->boolean('is_default')->default(false);
                $table->enum('status', ['Active', 'Inactive'])->default('Active');
                $table->text('description')->nullable();
                $table->timestamps();
            });

            // Seed initial policies
            DB::table('payroll_working_days_settings')->insert([
                [
                    'policy_name' => 'Standard 26 Working Days (Fixed)',
                    'calculation_basis' => 'fixed_26_days',
                    'weekly_off_pattern' => 'sunday_only',
                    'deduct_unpaid_leave_only' => true,
                    'include_holidays_in_payable' => true,
                    'include_weekly_off_in_payable' => true,
                    'is_default' => true,
                    'status' => 'Active',
                    'description' => 'Standard Indian payroll rule with 26 fixed divisor days. LWP deduction = (Gross / 26) * LWP Days.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'policy_name' => 'Calendar Month Days (Actual 28/30/31 Days)',
                    'calculation_basis' => 'calendar_days',
                    'weekly_off_pattern' => 'sunday_only',
                    'deduct_unpaid_leave_only' => true,
                    'include_holidays_in_payable' => true,
                    'include_weekly_off_in_payable' => true,
                    'is_default' => false,
                    'status' => 'Active',
                    'description' => 'Prorated based on actual days in the calendar month. LWP deduction = (Gross / Calendar Days) * LWP Days.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'policy_name' => '5-Day Week Policy (Mon-Fri, Sat-Sun Off)',
                    'calculation_basis' => 'actual_working_days',
                    'weekly_off_pattern' => 'saturday_sunday',
                    'deduct_unpaid_leave_only' => true,
                    'include_holidays_in_payable' => true,
                    'include_weekly_off_in_payable' => true,
                    'is_default' => false,
                    'status' => 'Active',
                    'description' => 'Calculates per-day wage by dividing Gross by actual working days (excluding Saturdays, Sundays & Public Holidays).',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'policy_name' => 'Standard 30 Days Rule (Fixed)',
                    'calculation_basis' => 'fixed_30_days',
                    'weekly_off_pattern' => 'sunday_only',
                    'deduct_unpaid_leave_only' => true,
                    'include_holidays_in_payable' => true,
                    'include_weekly_off_in_payable' => true,
                    'is_default' => false,
                    'status' => 'Active',
                    'description' => 'Calculates per-day wage by dividing Gross by fixed 30 days every month.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_working_days_settings');
    }
};
