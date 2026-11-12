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
        Schema::table('salary_components', function (Blueprint $table) {
            if (!Schema::hasColumn('salary_components', 'designation_id')) {
                $table->unsignedBigInteger('designation_id')->nullable()->after('component_code')->index();
            }
            if (!Schema::hasColumn('salary_components', 'state_id')) {
                $table->unsignedBigInteger('state_id')->nullable()->after('designation_id')->index();
            }
            if (!Schema::hasColumn('salary_components', 'city_id')) {
                $table->unsignedBigInteger('city_id')->nullable()->after('state_id')->index();
            }
        });

        // Drop strict single-column unique index on component_code if exists, to allow designation/city-specific variants
        try {
            $indexes = DB::select("SHOW INDEX FROM salary_components WHERE Key_name = 'salary_components_component_code_unique'");
            if (!empty($indexes)) {
                Schema::table('salary_components', function (Blueprint $table) {
                    $table->dropUnique('salary_components_component_code_unique');
                    $table->index('component_code');
                });
            }
        } catch (\Exception $e) {
            // Ignore if already dropped
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_components', function (Blueprint $table) {
            if (Schema::hasColumn('salary_components', 'city_id')) {
                $table->dropColumn('city_id');
            }
            if (Schema::hasColumn('salary_components', 'state_id')) {
                $table->dropColumn('state_id');
            }
            if (Schema::hasColumn('salary_components', 'designation_id')) {
                $table->dropColumn('designation_id');
            }
        });
    }
};
