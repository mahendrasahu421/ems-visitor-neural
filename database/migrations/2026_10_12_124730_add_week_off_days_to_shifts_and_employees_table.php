<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('shifts', 'week_off_days')) {
            Schema::table('shifts', function (Blueprint $table) {
                $table->json('week_off_days')->nullable()->after('grace_period');
            });
        }

        if (!Schema::hasColumn('employees', 'week_off_days')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->json('week_off_days')->nullable()->after('shift_id');
            });
        }

        if (!Schema::hasColumn('payroll_working_days_settings', 'custom_week_off_days')) {
            Schema::table('payroll_working_days_settings', function (Blueprint $table) {
                $table->json('custom_week_off_days')->nullable()->after('weekly_off_pattern');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('shifts', 'week_off_days')) {
            Schema::table('shifts', function (Blueprint $table) {
                $table->dropColumn('week_off_days');
            });
        }

        if (Schema::hasColumn('employees', 'week_off_days')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('week_off_days');
            });
        }

        if (Schema::hasColumn('payroll_working_days_settings', 'custom_week_off_days')) {
            Schema::table('payroll_working_days_settings', function (Blueprint $table) {
                $table->dropColumn('custom_week_off_days');
            });
        }
    }
};
