<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('patId', 15)->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->integer('posting_state')->nullable();
            $table->integer('posting_city')->nullable();
            $table->integer('posting_district')->nullable();
            $table->date('joining_date')->nullable();
            $table->string('band', 50)->nullable();
            $table->integer('department_id')->nullable();
            $table->date('date_of_confirmation')->nullable();
            $table->string('employment_type', 100)->nullable();
            $table->date('retirement_date')->nullable();
            $table->integer('designation_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->integer('offline_review')->nullable();
            $table->tinyInteger('pmp_type')->nullable()->comment('1=Online, 2=Offline, 3=Not Eligible');
            $table->float('last_year_pli')->nullable();
            $table->float('next_year_pli')->nullable();
            $table->string('increment_last_yr', 70)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_profiles');
    }
};
