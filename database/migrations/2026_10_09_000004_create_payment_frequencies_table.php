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
        if (!Schema::hasTable('payment_frequencies')) {
            Schema::create('payment_frequencies', function (Blueprint $table) {
                $table->id();
                $table->string('frequency_name', 100);
                $table->string('frequency_code', 50)->unique();
                $table->unsignedSmallInteger('cycles_per_year')->default(12);
                $table->unsignedSmallInteger('days_per_cycle')->default(30);
                $table->string('pay_day_rule', 150)->nullable();
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
        Schema::dropIfExists('payment_frequencies');
    }
};
