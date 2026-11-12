<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDocumentsAndAddressToEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('pan_card')->nullable()->after('profile_image');
            $table->string('aadhar_card')->nullable()->after('pan_card');
            $table->string('voter_id')->nullable()->after('aadhar_card');
            $table->string('cv')->nullable()->after('voter_id');
            $table->text('address')->nullable()->after('cv');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['pan_card', 'aadhar_card', 'voter_id', 'cv', 'address']);
        });
    }
}
