<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('leaves', function (Blueprint $table) {
        // $table->unsignedBigInteger('employee_id')->after('id');
        // $table->unsignedBigInteger('leave_type_id')->after('employee_id');
        // $table->date('from_date')->after('leave_type_id');
        // $table->date('to_date')->after('from_date');
        // $table->text('reason')->nullable()->after('to_date');
        // $table->string('status')->default('Pending')->after('reason');

      
    });
}

    /**
     * Reverse the migrations.
     */
   public function down()
{
    Schema::table('leaves', function (Blueprint $table) {
        $table->dropForeign(['employee_id']);
        $table->dropForeign(['leave_type_id']);
        $table->dropColumn(['employee_id', 'leave_type_id', 'from_date', 'to_date', 'reason', 'status']);
    });
}

};
