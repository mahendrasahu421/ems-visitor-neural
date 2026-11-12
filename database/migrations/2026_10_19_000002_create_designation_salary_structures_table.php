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
        if (!Schema::hasTable('designation_salary_structures')) {
            Schema::create('designation_salary_structures', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('designation_id')->unique();
                $table->decimal('monthly_gross', 12, 2)->default(0.00);
                $table->decimal('basic_salary_pct', 5, 2)->default(50.00);
                $table->decimal('hra_pct', 5, 2)->default(25.00);
                $table->decimal('conveyance_allowance', 12, 2)->default(0.00);
                $table->decimal('medical_allowance', 12, 2)->default(0.00);
                $table->decimal('bike_allowance', 12, 2)->default(0.00);
                $table->decimal('special_allowance', 12, 2)->default(0.00);
                $table->decimal('other_allowance', 12, 2)->default(0.00);
                $table->boolean('is_pf_enabled')->default(true);
                $table->boolean('is_esi_enabled')->default(true);
                $table->boolean('is_pt_enabled')->default(true);
                $table->boolean('is_gratuity_enabled')->default(true);
                $table->boolean('apply_state_pt_rule')->default(true);
                $table->boolean('apply_metro_hra_rule')->default(true);
                $table->string('status', 20)->default('Active');
                $table->text('remarks')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designation_salary_structures');
    }
};
