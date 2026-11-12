<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change enum values to 'Active' and 'Inactive'
        Schema::table('departments', function (Blueprint $table) {
            $table->enum('status', ['Active', 'Inactive'])->default('Active')->change();
        });

        // Update existing lowercase values to capitalized ones
        DB::table('departments')
            ->where('status', 'active')
            ->update(['status' => 'Active']);

        DB::table('departments')
            ->where('status', 'inactive')
            ->update(['status' => 'Inactive']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive'])->default('active')->change();
        });

        DB::table('departments')
            ->where('status', 'Active')
            ->update(['status' => 'active']);

        DB::table('departments')
            ->where('status', 'Inactive')
            ->update(['status' => 'inactive']);
    }
};
