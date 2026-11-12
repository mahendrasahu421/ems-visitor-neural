<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('department_id');
            $table->string('name');
            $table->string('code')->unique();
            $table->decimal('kpi_weightage', 5, 2)->default(0);
            $table->decimal('competency_weight', 5, 2)->default(0);
            $table->enum('status', ['Active', 'Inactive']);
            $table->timestamps();
            $table->softDeletes(); // ✅ Soft delete support added

            // 🔗 Foreign Keys
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('designations');
    }
};
