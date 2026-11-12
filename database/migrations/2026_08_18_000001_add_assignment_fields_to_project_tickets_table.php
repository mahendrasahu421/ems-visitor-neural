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
            if (!Schema::hasColumn('project_tickets', 'assigned_employee_id')) {
                $table->unsignedBigInteger('assigned_employee_id')->nullable()->after('project_id')->index();
            }

            if (!Schema::hasColumn('project_tickets', 'assigned_at')) {
                $table->timestamp('assigned_at')->nullable()->after('admin_note');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('project_tickets')) {
            return;
        }

        Schema::table('project_tickets', function (Blueprint $table) {
            if (Schema::hasColumn('project_tickets', 'assigned_employee_id')) {
                $table->dropIndex(['assigned_employee_id']);
                $table->dropColumn('assigned_employee_id');
            }

            if (Schema::hasColumn('project_tickets', 'assigned_at')) {
                $table->dropColumn('assigned_at');
            }
        });
    }
};
