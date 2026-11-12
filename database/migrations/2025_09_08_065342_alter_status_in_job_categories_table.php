<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('job_categories', function (Blueprint $table) {
            // pehle boolean ko drop karo
            $table->dropColumn('status');
        });

        Schema::table('job_categories', function (Blueprint $table) {
            // naya enum column add karo
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
        });
    }

    public function down(): void
    {
        Schema::table('job_categories', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('job_categories', function (Blueprint $table) {
            $table->boolean('status')->default(true);
        });
    }
};
