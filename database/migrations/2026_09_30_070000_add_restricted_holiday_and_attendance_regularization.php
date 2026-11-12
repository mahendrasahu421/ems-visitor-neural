<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\LeaveType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add holiday_id to leaves table if not exists
        if (Schema::hasTable('leaves') && !Schema::hasColumn('leaves', 'holiday_id')) {
            Schema::table('leaves', function (Blueprint $table) {
                $table->unsignedBigInteger('holiday_id')->nullable()->after('leave_type_id');
            });
        }

        // 2. Add regularization columns to employee_attendances table if not exists
        if (Schema::hasTable('employee_attendances')) {
            Schema::table('employee_attendances', function (Blueprint $table) {
                if (!Schema::hasColumn('employee_attendances', 'is_regularized')) {
                    $table->boolean('is_regularized')->default(false)->after('status');
                }
                if (!Schema::hasColumn('employee_attendances', 'regularized_by')) {
                    $table->unsignedBigInteger('regularized_by')->nullable()->after('is_regularized');
                }
                if (!Schema::hasColumn('employee_attendances', 'regularization_reason')) {
                    $table->text('regularization_reason')->nullable()->after('regularized_by');
                }
            });
        }

        // 3. Ensure 'Restricted Holiday' exists in leave_types table
        $existingRh = DB::table('leave_types')
            ->where('leave_name', 'like', '%Restricted%')
            ->orWhere('leave_code', 'RH-001')
            ->first();

        if (!$existingRh) {
            DB::table('leave_types')->insert([
                'company_id' => 1,
                'agency_id' => null,
                'leave_name' => 'Restricted Holiday',
                'leave_code' => 'RH-001',
                'total_leaves' => 5,
                'carry_forward' => 0,
                'encashable' => 0,
                'applicable_for' => 'All',
                'leave_category' => 'Paid',
                'status' => 'Active',
                'description' => 'Restricted / Optional holiday leave. Employees can select up to 5 declared restricted festival holidays per calendar year.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('leaves') && Schema::hasColumn('leaves', 'holiday_id')) {
            Schema::table('leaves', function (Blueprint $table) {
                $table->dropColumn('holiday_id');
            });
        }

        if (Schema::hasTable('employee_attendances')) {
            Schema::table('employee_attendances', function (Blueprint $table) {
                if (Schema::hasColumn('employee_attendances', 'is_regularized')) {
                    $table->dropColumn('is_regularized');
                }
                if (Schema::hasColumn('employee_attendances', 'regularized_by')) {
                    $table->dropColumn('regularized_by');
                }
                if (Schema::hasColumn('employee_attendances', 'regularization_reason')) {
                    $table->dropColumn('regularization_reason');
                }
            });
        }
    }
};
