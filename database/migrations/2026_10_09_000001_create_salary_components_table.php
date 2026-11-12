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
        if (!Schema::hasTable('salary_components')) {
            Schema::create('salary_components', function (Blueprint $table) {
                $table->id();
                $table->string('component_name', 150);
                $table->string('component_code', 50)->unique();
                $table->enum('type', ['earning', 'deduction'])->default('earning');
                $table->enum('calculation_type', ['percentage', 'fixed'])->default('percentage');
                $table->decimal('percentage', 5, 2)->nullable()->default(0);
                $table->decimal('fixed_amount', 12, 2)->nullable()->default(0);
                $table->boolean('is_taxable')->default(true);
                $table->boolean('is_statutory')->default(false);
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
        Schema::dropIfExists('salary_components');
    }
};
