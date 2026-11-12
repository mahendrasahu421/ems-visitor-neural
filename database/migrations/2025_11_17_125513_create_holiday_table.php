<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('holidays', function (Blueprint $table) {

            $table->increments('id'); // Primary Key

            $table->string('holiday_name', 100);
            $table->date('holiday_date');
            $table->string('holiday_remark', 255);

            // Status: 1 = Active, 0 = Inactive
            $table->integer('status')->default(1);

            $table->dateTime('creation_date')->nullable();
            $table->dateTime('modification_date')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('holidays');
    }
};
