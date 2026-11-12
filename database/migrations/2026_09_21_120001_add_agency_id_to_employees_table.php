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
        if (Schema::hasTable('employees') && !Schema::hasColumn('employees', 'agency_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->unsignedBigInteger('agency_id')->nullable()->after('designation_id');

                $table->foreign('agency_id')
                    ->references('id')
                    ->on('agencies')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('employees') && Schema::hasColumn('employees', 'agency_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropForeign(['agency_id']);
                $table->dropColumn('agency_id');
            });
        }
    }
};
