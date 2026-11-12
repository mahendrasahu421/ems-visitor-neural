<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('job_hrms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('category_id')->nullable()->constrained('job_categories')->nullOnDelete();
            $table->json('skills')->nullable();
            $table->integer('positions')->default(1);
            $table->enum('job_type', ['Full-Time', 'Part-Time', 'Contract'])->default('Full-Time');
            $table->decimal('ctc_from', 8, 2)->nullable();
            $table->decimal('ctc_to', 8, 2)->nullable();
            $table->integer('min_experience')->nullable();
            $table->integer('max_experience')->nullable();
            $table->json('locations')->nullable();
            $table->text('description')->nullable();
            $table->string('keywords')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });

    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs_hrms');
    }
};
