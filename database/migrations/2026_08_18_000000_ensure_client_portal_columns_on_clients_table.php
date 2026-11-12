<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('clients')) {
            return;
        }

        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'role_id')) {
                $table->unsignedBigInteger('role_id')->nullable()->after('id');

                if (Schema::hasTable('roles')) {
                    $table->foreign('role_id')->references('id')->on('roles')->nullOnDelete();
                }
            }

            if (!Schema::hasColumn('clients', 'invoice_client_id')) {
                $table->unsignedInteger('invoice_client_id')->nullable()->after('role_id')->index();
            }

            if (!Schema::hasColumn('clients', 'remember_token')) {
                $table->rememberToken();
            }

            if (!Schema::hasColumn('clients', 'invoiceDate')) {
                $table->date('invoiceDate')->nullable()->after('status');
            }

            if (!Schema::hasColumn('clients', 'client_name')) {
                $table->string('client_name')->nullable()->after('invoiceDate');
            }

            if (!Schema::hasColumn('clients', 'concern_person')) {
                $table->string('concern_person')->nullable()->after('client_name');
            }

            if (!Schema::hasColumn('clients', 'cin')) {
                $table->string('cin')->nullable()->after('concern_person');
            }

            if (!Schema::hasColumn('clients', 'website')) {
                $table->text('website')->nullable()->after('cin');
            }

            if (!Schema::hasColumn('clients', 'mobile')) {
                $table->string('mobile', 20)->nullable()->after('website');
            }

            if (!Schema::hasColumn('clients', 'country_id')) {
                $table->unsignedInteger('country_id')->nullable()->after('mobile');
            }

            if (!Schema::hasColumn('clients', 'state_id')) {
                $table->unsignedInteger('state_id')->nullable()->after('country_id');
            }

            if (!Schema::hasColumn('clients', 'address')) {
                $table->text('address')->nullable()->after('state_id');
            }

            if (!Schema::hasColumn('clients', 'gst_number')) {
                $table->string('gst_number')->nullable()->after('address');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('clients')) {
            return;
        }

        Schema::table('clients', function (Blueprint $table) {
            foreach ([
                'invoice_client_id',
                'remember_token',
                'invoiceDate',
                'client_name',
                'concern_person',
                'cin',
                'website',
                'mobile',
                'country_id',
                'state_id',
                'address',
                'gst_number',
            ] as $column) {
                if (Schema::hasColumn('clients', $column)) {
                    $table->dropColumn($column);
                }
            }

            if (Schema::hasColumn('clients', 'role_id')) {
                if (Schema::hasTable('roles')) {
                    $table->dropForeign(['role_id']);
                }

                $table->dropColumn('role_id');
            }
        });
    }
};
