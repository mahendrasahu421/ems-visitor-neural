<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'country') 
                && !Schema::hasColumn('employees', 'posting_country')) {
                $table->renameColumn('country', 'posting_country');
            }
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'posting_country')) {
                $table->renameColumn('posting_country', 'country');
            }
        });
    }
};
