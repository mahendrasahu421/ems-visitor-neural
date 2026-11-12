<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id'); // 🔹 Company ID (Foreign Key)
            $table->string('branch_name');
            $table->string('branch_code')->unique();
            $table->string('branch_owner_name');
            $table->string('contact_number', 15);
            $table->string('gst_number')->nullable();
            $table->string('country');
            $table->string('state');
            $table->string('city');
            $table->string('address_1');
            $table->string('address_2')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();

            // 🔹 Foreign Key Constraint
            $table->foreign('company_id')
                  ->references('id')
                  ->on('companies')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
