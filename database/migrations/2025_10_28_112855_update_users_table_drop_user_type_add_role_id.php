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
        Schema::table('users', function (Blueprint $table) {
            // Drop old 'user_type' column if it exists
            if (Schema::hasColumn('users', 'user_type')) {
                $table->dropColumn('user_type');
            }

            // Add 'role_id' only if it doesn't exist
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->unsignedBigInteger('role_id')->nullable()->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        if (Schema::hasColumn('users', 'role_id')) {
            $table->dropColumn('role_id');
        }

        if (!Schema::hasColumn('users', 'user_type')) {
            $table->string('user_type')->nullable();
        }
    });
}

};
