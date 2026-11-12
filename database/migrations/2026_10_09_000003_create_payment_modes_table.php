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
        if (!Schema::hasTable('payment_modes')) {
            Schema::create('payment_modes', function (Blueprint $table) {
                $table->id();
                $table->string('mode_name', 100);
                $table->string('mode_code', 50)->unique();
                $table->enum('channel_type', ['electronic', 'physical'])->default('electronic');
                $table->boolean('requires_bank_details')->default(true);
                $table->decimal('processing_fee', 8, 2)->default(0.00);
                $table->boolean('is_default')->default(false);
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
        Schema::dropIfExists('payment_modes');
    }
};
