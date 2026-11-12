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
        if (Schema::hasTable('salary_structures')) {
            Schema::table('salary_structures', function (Blueprint $table) {
                if (!Schema::hasColumn('salary_structures', 'bike_allowance')) {
                    $table->decimal('bike_allowance', 12, 2)->default(1500.00)->after('other_allowance');
                }
                if (!Schema::hasColumn('salary_structures', 'gratuity')) {
                    $table->decimal('gratuity', 12, 2)->default(0.00)->after('bike_allowance');
                }
            });
        }

        if (Schema::hasTable('payslips')) {
            Schema::table('payslips', function (Blueprint $table) {
                if (!Schema::hasColumn('payslips', 'earned_bike_allowance')) {
                    $table->decimal('earned_bike_allowance', 12, 2)->default(0.00)->after('earned_special_allowance');
                }
                if (!Schema::hasColumn('payslips', 'gratuity_accrual')) {
                    $table->decimal('gratuity_accrual', 12, 2)->default(0.00)->after('other_deductions');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('salary_structures')) {
            Schema::table('salary_structures', function (Blueprint $table) {
                if (Schema::hasColumn('salary_structures', 'bike_allowance')) {
                    $table->dropColumn('bike_allowance');
                }
                if (Schema::hasColumn('salary_structures', 'gratuity')) {
                    $table->dropColumn('gratuity');
                }
            });
        }

        if (Schema::hasTable('payslips')) {
            Schema::table('payslips', function (Blueprint $table) {
                if (Schema::hasColumn('payslips', 'earned_bike_allowance')) {
                    $table->dropColumn('earned_bike_allowance');
                }
                if (Schema::hasColumn('payslips', 'gratuity_accrual')) {
                    $table->dropColumn('gratuity_accrual');
                }
            });
        }
    }
};
