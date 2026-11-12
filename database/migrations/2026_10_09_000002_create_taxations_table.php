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
        if (!Schema::hasTable('taxations')) {
            Schema::create('taxations', function (Blueprint $table) {
                $table->id();
                $table->string('tax_name', 150);
                $table->string('tax_code', 50)->unique();
                $table->enum('type', ['deduction', 'employer_contribution', 'statutory_tax'])->default('deduction');
                $table->enum('applicable_on', ['basic', 'gross', 'net', 'taxable_income', 'ctc'])->default('basic');
                $table->enum('calculation_basis', ['percentage', 'fixed_slab'])->default('percentage');
                $table->decimal('rate', 6, 2)->default(0.00);
                $table->decimal('fixed_amount', 12, 2)->default(0.00);
                $table->decimal('threshold_limit', 12, 2)->nullable()->default(0.00);
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
        Schema::dropIfExists('taxations');
    }
};
