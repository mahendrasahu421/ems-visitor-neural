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
        if (!Schema::hasTable('employee_increments')) {
            Schema::create('employee_increments', function (Blueprint $table) {
                $table->id();
                $table->string('increment_number', 50)->unique();
                $table->unsignedBigInteger('employee_id')->index();
                $table->string('review_cycle', 100)->default('Annual Appraisal');
                $table->date('effective_date');

                // Existing Compensation Snapshot
                $table->decimal('current_ctc', 12, 2)->default(0);
                $table->decimal('current_gross', 12, 2)->default(0);

                // Increment Parameters
                $table->enum('increment_type', ['percentage', 'fixed_amount', 'direct_ctc'])->default('percentage');
                $table->decimal('increment_value', 12, 2)->default(0); // e.g. 10.00 for 10% or 5000.00
                $table->decimal('hike_percentage', 6, 2)->default(0); // Computed percentage hike
                $table->decimal('hike_amount', 12, 2)->default(0);     // Annual CTC increase amount

                // Revised Compensation
                $table->decimal('new_ctc', 12, 2)->default(0);
                $table->decimal('new_gross', 12, 2)->default(0);

                // Designation / Promotion Details
                $table->unsignedBigInteger('previous_designation_id')->nullable();
                $table->unsignedBigInteger('new_designation_id')->nullable();

                // Salary Structure References
                $table->unsignedBigInteger('previous_salary_structure_id')->nullable();
                $table->unsignedBigInteger('new_salary_structure_id')->nullable();

                // Appraisal & Review Metadata
                $table->string('performance_rating', 100)->nullable();
                $table->enum('status', ['Draft', 'Approved', 'Applied', 'Rejected'])->default('Applied');
                $table->text('remarks')->nullable();
                $table->text('letter_notes')->nullable();

                // Audit & Workflow
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamp('applied_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_increments');
    }
};
