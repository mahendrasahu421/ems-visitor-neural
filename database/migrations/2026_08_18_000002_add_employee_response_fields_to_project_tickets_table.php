<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('project_tickets')) {
            return;
        }

        Schema::table('project_tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('project_tickets', 'employee_status')) {
                $table->string('employee_status', 50)->nullable()->after('status');
            }

            if (!Schema::hasColumn('project_tickets', 'employee_note')) {
                $table->text('employee_note')->nullable()->after('admin_note');
            }

            if (!Schema::hasColumn('project_tickets', 'employee_responded_at')) {
                $table->timestamp('employee_responded_at')->nullable()->after('employee_note');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('project_tickets')) {
            return;
        }

        Schema::table('project_tickets', function (Blueprint $table) {
            foreach (['employee_status', 'employee_note', 'employee_responded_at'] as $column) {
                if (Schema::hasColumn('project_tickets', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
