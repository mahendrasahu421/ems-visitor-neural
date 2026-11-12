<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {

            if (Schema::hasColumn('employees', 'increment_last_yr')) {
                $table->dropColumn('increment_last_yr');
            }

            if (Schema::hasColumn('employees', 'account_status')) {
                $table->dropColumn('account_status');
            }

            if (Schema::hasColumn('employees', 'pmp_type')) {
                $table->dropColumn('pmp_type');
            }

            if (Schema::hasColumn('employees', 'offline_review')) {
                $table->dropColumn('offline_review');
            }

            if (Schema::hasColumn('employees', 'retirement_date')) {
                $table->dropColumn('retirement_date');
            }
            if (!Schema::hasColumn('employees', 'country')) {
                $table->string('country')->nullable()->after('employee_email');
            }
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {

            if (!Schema::hasColumn('employees', 'increment_last_yr')) {
                $table->string('increment_last_yr')->nullable();
            }

            if (!Schema::hasColumn('employees', 'account_status')) {
                $table->string('account_status')->nullable();
            }

            if (!Schema::hasColumn('employees', 'pmp_type')) {
                $table->string('pmp_type')->nullable();
            }

            if (!Schema::hasColumn('employees', 'offline_review')) {
                $table->string('offline_review')->nullable();
            }

            if (!Schema::hasColumn('employees', 'retirement_date')) {
                $table->date('retirement_date')->nullable();
            }

            if (Schema::hasColumn('employees', 'country')) {
                $table->dropColumn('country');
            }
        });
    }
};
