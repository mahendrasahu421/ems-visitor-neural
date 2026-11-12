<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('designations')) {
            $exists = DB::table('designations')->where('name', 'Supervisor')->exists();
            if (!$exists) {
                DB::table('designations')->insert([
                    'company_id' => 1,
                    'name' => 'Supervisor',
                    'code' => 'SUP',
                    'status' => 'Active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('designations')) {
            DB::table('designations')->where('code', 'SUP')->delete();
        }
    }
};
