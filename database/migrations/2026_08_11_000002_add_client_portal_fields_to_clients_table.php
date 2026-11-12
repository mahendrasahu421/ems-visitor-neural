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
            if (!Schema::hasColumn('clients', 'profile')) {
                $table->string('profile')->nullable()->after('id');
            }

            if (!Schema::hasColumn('clients', 'first_name')) {
                $table->string('first_name')->nullable()->after('profile');
            }

            if (!Schema::hasColumn('clients', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }

            if (!Schema::hasColumn('clients', 'username')) {
                $table->string('username')->nullable()->after('last_name');
            }

            if (!Schema::hasColumn('clients', 'password')) {
                $table->string('password')->nullable()->after('email');
            }

            if (!Schema::hasColumn('clients', 'phone')) {
                $table->string('phone', 20)->nullable()->after('password');
            }

            if (!Schema::hasColumn('clients', 'company_name')) {
                $table->string('company_name')->nullable()->after('phone');
            }

            if (!Schema::hasColumn('clients', 'status')) {
                $table->enum('status', ['Active', 'Inactive'])->default('Active')->after('company_name');
            }

            if (!Schema::hasColumn('clients', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }

            if (!Schema::hasColumn('clients', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });

        DB::table('clients')->whereNull('first_name')->update([
            'first_name' => DB::raw('client_name'),
        ]);

        DB::table('clients')->whereNull('phone')->update([
            'phone' => DB::raw('mobile'),
        ]);

        DB::table('clients')->whereNull('company_name')->update([
            'company_name' => DB::raw('client_name'),
        ]);

        DB::statement("UPDATE clients SET username = CONCAT('client', id) WHERE username IS NULL OR username = ''");
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            foreach (['profile', 'first_name', 'last_name', 'username', 'password', 'phone', 'company_name', 'status', 'created_at', 'updated_at'] as $column) {
                if (Schema::hasColumn('clients', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
