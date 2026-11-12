<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();

            // Employee who applied for leave
            $table->unsignedBigInteger('employee_id');

            // Leave Type
            $table->unsignedBigInteger('leave_type_id');

            // Leave Dates
            $table->date('from_date');
            $table->date('to_date');

            // Duration
            $table->integer('duration')->nullable();

            // Reason
            $table->text('reason');

            // Status
            $table->enum('status', ['DRAFT', 'SENT', 'APPROVED', 'REJECTED'])->default('DRAFT');

            $table->timestamps();

            // ✅ FIXED FOREIGN KEYS
            $table->foreign('employee_id')
                  ->references('employee_id')   // <-- Correct PK column
                  ->on('employees')             // <-- Correct table name
                  ->onDelete('cascade');

            $table->foreign('leave_type_id')
                  ->references('id')
                  ->on('leave_types')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('leaves');
    }
};
