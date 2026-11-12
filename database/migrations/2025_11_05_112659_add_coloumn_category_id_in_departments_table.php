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
        Schema::table('departments', function (Blueprint $table) {
            // Add category_id column after 'id' if it doesn't already exist
            if (!Schema::hasColumn('departments', 'category_id')) {
                $table->unsignedBigInteger('category_id')->after('id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            // Drop the column when rolling back
            if (Schema::hasColumn('departments', 'category_id')) {
                $table->dropColumn('category_id');
            }
        });
    }
};
