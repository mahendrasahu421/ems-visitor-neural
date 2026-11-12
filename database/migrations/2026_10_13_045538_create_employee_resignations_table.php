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
        Schema::create('employee_resignations', function (Blueprint $table) {
            $table->id();
            $table->string('resignation_code', 30)->unique();
            $table->unsignedBigInteger('employee_id')->index();
            $table->enum('initiated_by', ['employee', 'supervisor', 'admin'])->default('employee');
            $table->unsignedBigInteger('initiator_id')->nullable();
            $table->unsignedBigInteger('supervisor_id')->nullable()->index();
            $table->date('resignation_date');
            $table->date('requested_last_working_day');
            $table->date('approved_last_working_day')->nullable();
            $table->integer('notice_period_days')->default(30);
            $table->string('reason', 150);
            $table->text('reason_details')->nullable();
            $table->json('cc_emails')->nullable();
            $table->enum('status', ['pending', 'in_review', 'approved', 'rejected', 'withdrawn', 'completed'])->default('pending');
            $table->string('action_by_type', 20)->nullable();
            $table->unsignedBigInteger('action_by_id')->nullable();
            $table->string('action_by_name', 150)->nullable();
            $table->dateTime('action_at')->nullable();
            $table->text('action_remarks')->nullable();
            $table->string('decision_token', 64)->unique();
            $table->string('document_path', 255)->nullable();
            $table->unsignedTinyInteger('feedback_rating')->nullable();
            $table->text('feedback_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_resignations');
    }
};
