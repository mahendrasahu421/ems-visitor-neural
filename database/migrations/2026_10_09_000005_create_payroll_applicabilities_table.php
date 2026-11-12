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
        if (!Schema::hasTable('payroll_applicabilities')) {
            Schema::create('payroll_applicabilities', function (Blueprint $table) {
                $table->id();
                $table->string('rule_name', 150);
                $table->string('rule_code', 50)->unique();
                $table->enum('applies_to', ['all', 'permanent', 'contractual', 'intern', 'probation'])->default('all');
                $table->string('salary_condition', 150)->nullable();
                $table->decimal('min_salary', 12, 2)->default(0.00);
                $table->decimal('max_salary', 12, 2)->default(0.00);
                $table->unsignedInteger('min_service_days')->default(0);
                $table->date('effective_from')->nullable();
                $table->enum('status', ['Active', 'Inactive'])->default('Active');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_applicabilities');
    }
};
