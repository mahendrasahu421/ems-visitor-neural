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
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('leave_name');
            $table->string('leave_code')->unique();
            $table->integer('total_leaves')->default(0);
            $table->boolean('carry_forward')->default(0)->comment('1 = Yes, 0 = No');
            $table->boolean('encashable')->default(0)->comment('1 = Yes, 0 = No');
            $table->enum('applicable_for', ['Male', 'Female', 'All'])->default('All');
            $table->enum('leave_category', ['Paid', 'Unpaid', 'Special'])->default('Paid');
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->text('description')->nullable();
            $table->timestamps();

            // 🔹 Remove foreign key if companies table not confirmed
            // If companies table definitely exists, uncomment the below:
            // $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};
