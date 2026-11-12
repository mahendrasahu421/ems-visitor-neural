<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id('employee_id');
            $table->string('patId', 15);
            $table->string('employee_name', 50);
            $table->string('employee_email', 50);
            $table->string('employee_mobile', 20);
            $table->string('employee_password', 100);
            $table->integer('employee_gender');
            $table->integer('posting_state');
            $table->integer('posting_city');
            $table->integer('posting_district');
            $table->date('joining_date');
            $table->string('band', 50);
            $table->integer('department_id');
            $table->date('date_of_confirmation')->nullable();
            $table->string('employment_type', 100);
            $table->date('retirement_date');
            $table->integer('designation_id');
            $table->enum('status', ['Active', 'Inactive']);
            $table->integer('offline_review');
            $table->integer('pmp_type')->comment('1=Online,2=Offline,3=not eligible');
            $table->integer('category_id');
            $table->float('last_year_pli');
            $table->float('next_year_pli');
            $table->integer('account_status')->default(0);
            $table->string('increment_last_yr', 70);
            $table->string('profile_image', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
