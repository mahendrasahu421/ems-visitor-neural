<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'invoice_client_id')) {
                $table->unsignedInteger('invoice_client_id')->nullable()->after('role_id');
                $table->index('invoice_client_id');
            }
        });

        if (Schema::hasTable('clientmaster') && Schema::hasColumn('clients', 'invoice_client_id')) {
            DB::table('clients as c')
                ->join('clientmaster as cm', 'c.email', '=', 'cm.EmailAddress')
                ->whereNull('c.invoice_client_id')
                ->update(['c.invoice_client_id' => DB::raw('cm.ClientID')]);
        }
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (Schema::hasColumn('clients', 'invoice_client_id')) {
                $table->dropIndex(['invoice_client_id']);
                $table->dropColumn('invoice_client_id');
            }
        });
    }
};
