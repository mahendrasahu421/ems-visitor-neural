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
        Schema::table('users', function (Blueprint $table) {
            // 1️⃣ Drop existing column 'user_type'
            if (Schema::hasColumn('users', 'user_type')) {
                $table->dropColumn('user_type');
            }

            // 2️⃣ Add new column 'role_id'
            $table->unsignedBigInteger('role_id')->nullable()->after('id');

            // Optional: foreign key (agar roles table exist karta ho)
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
     public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Rollback: add back user_type
            $table->string('user_type')->nullable()->after('id');

            // Drop foreign key and role_id column
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};
