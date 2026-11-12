<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dailyreport', function (Blueprint $table) {
            $table->increments('report_ID');

            $table->integer('ProjectID')->unsigned();
            $table->integer('EmpId')->unsigned();

            $table->text('WorkDescription')
                ->charset('latin1')
                ->collation('latin1_swedish_ci');

            $table->string('StartingTime', 10)
                ->charset('latin1')
                ->collation('latin1_swedish_ci');

            $table->string('EndingTime', 10)
                ->charset('latin1')
                ->collation('latin1_swedish_ci');

            $table->string('TotalHours', 10)
                ->charset('latin1')
                ->collation('latin1_swedish_ci');

            $table->dateTime('DrEntryDate')
                ->nullable()
                ->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->dateTime('CreationDate');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dailyreport');
    }
};
