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
        if (!Schema::hasTable('payroll_visibility_controls')) {
            Schema::create('payroll_visibility_controls', function (Blueprint $table) {
                $table->id();
                $table->string('rule_name', 150);
                $table->string('rule_code', 50)->unique();
                $table->string('applies_to', 100);
                $table->enum('access_level', ['Full Access', 'Read Only', 'Restricted', 'Self Only'])->default('Read Only');
                $table->boolean('can_view_salary')->default(true);
                $table->boolean('can_edit_structure')->default(false);
                $table->boolean('can_process_payroll')->default(false);
                $table->boolean('can_download_reports')->default(false);
                $table->boolean('mask_sensitive_components')->default(false);
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
        Schema::dropIfExists('payroll_visibility_controls');
    }
};
