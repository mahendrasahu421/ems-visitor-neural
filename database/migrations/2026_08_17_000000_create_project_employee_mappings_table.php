<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('project_employee_mappings')) {
            return;
        }

        Schema::create('project_employee_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('project_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('project_role', 100)->nullable();
            $table->unsignedTinyInteger('allocation_percent')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->unique(['project_id', 'employee_id'], 'uk_project_employee_mapping');
            $table->index('project_id', 'idx_project_employee_project');
            $table->index('employee_id', 'idx_project_employee_employee');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_employee_mappings');
    }
};
