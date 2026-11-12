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
        // 1. Add supervisor_id to employees table if not present
        if (Schema::hasTable('employees') && !Schema::hasColumn('employees', 'supervisor_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->unsignedBigInteger('supervisor_id')->nullable()->after('designation_id');
            });
        }

        // 2. Add workflow and document fields to leaves table
        if (Schema::hasTable('leaves')) {
            Schema::table('leaves', function (Blueprint $table) {
                if (!Schema::hasColumn('leaves', 'document_path')) {
                    $table->string('document_path')->nullable()->after('reason');
                }
                if (!Schema::hasColumn('leaves', 'approved_by')) {
                    $table->unsignedBigInteger('approved_by')->nullable()->after('status');
                }
                if (!Schema::hasColumn('leaves', 'rejected_by')) {
                    $table->unsignedBigInteger('rejected_by')->nullable()->after('approved_by');
                }
                if (!Schema::hasColumn('leaves', 'supervisor_remarks')) {
                    $table->text('supervisor_remarks')->nullable()->after('rejected_by');
                }
                if (!Schema::hasColumn('leaves', 'action_at')) {
                    $table->dateTime('action_at')->nullable()->after('supervisor_remarks');
                }
            });
        }

        // 3. Create leave_activity_logs table for comprehensive audit logs
        if (!Schema::hasTable('leave_activity_logs')) {
            Schema::create('leave_activity_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('leave_id');
                $table->string('actor_type', 50)->default('User'); // 'User', 'Employee', 'System'
                $table->unsignedBigInteger('actor_id')->nullable();
                $table->string('actor_name')->nullable();
                $table->string('action', 50); // 'applied', 'approved', 'rejected', 'cancelled', 'updated'
                $table->text('remarks')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('leave_id')
                    ->references('id')
                    ->on('leaves')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('leave_activity_logs')) {
            Schema::dropIfExists('leave_activity_logs');
        }

        if (Schema::hasTable('leaves')) {
            Schema::table('leaves', function (Blueprint $table) {
                $columns = ['document_path', 'approved_by', 'rejected_by', 'supervisor_remarks', 'action_at'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('leaves', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('employees') && Schema::hasColumn('employees', 'supervisor_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('supervisor_id');
            });
        }
    }
};
