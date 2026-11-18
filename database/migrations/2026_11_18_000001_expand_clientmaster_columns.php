<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('clientmaster')) {
            DB::statement("
                ALTER TABLE `clientmaster` 
                    MODIFY COLUMN `Fax` VARCHAR(100) NULL DEFAULT NULL,
                    MODIFY COLUMN `EmailAddress` VARCHAR(255) NOT NULL,
                    MODIFY COLUMN `ClientName` VARCHAR(255) NOT NULL,
                    MODIFY COLUMN `ConcernOwner` VARCHAR(150) NULL DEFAULT NULL,
                    MODIFY COLUMN `Address` TEXT NULL DEFAULT NULL,
                    MODIFY COLUMN `MobileNo` VARCHAR(50) NULL DEFAULT NULL,
                    MODIFY COLUMN `PhoneNo` VARCHAR(50) NULL DEFAULT NULL,
                    MODIFY COLUMN `Country` VARCHAR(100) NULL DEFAULT NULL,
                    MODIFY COLUMN `StateID` VARCHAR(50) NULL DEFAULT NULL,
                    MODIFY COLUMN `Priority` VARCHAR(5) NULL DEFAULT ''
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('clientmaster')) {
            DB::statement("
                ALTER TABLE `clientmaster` 
                    MODIFY COLUMN `Fax` VARCHAR(15) NOT NULL,
                    MODIFY COLUMN `EmailAddress` VARCHAR(30) NOT NULL,
                    MODIFY COLUMN `ClientName` VARCHAR(50) NOT NULL,
                    MODIFY COLUMN `ConcernOwner` VARCHAR(40) NOT NULL,
                    MODIFY COLUMN `Address` VARCHAR(100) NOT NULL,
                    MODIFY COLUMN `MobileNo` VARCHAR(15) NOT NULL,
                    MODIFY COLUMN `PhoneNo` VARCHAR(30) NULL DEFAULT NULL,
                    MODIFY COLUMN `Country` VARCHAR(40) NOT NULL,
                    MODIFY COLUMN `StateID` VARCHAR(11) NULL DEFAULT NULL
            ");
        }
    }
};
