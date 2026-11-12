<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('expenses') && !Schema::hasColumn('expenses', 'ClientID')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->unsignedInteger('ClientID')->nullable()->after('id')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('expenses') && Schema::hasColumn('expenses', 'ClientID')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->dropIndex(['ClientID']);
                $table->dropColumn('ClientID');
            });
        }
    }
};
