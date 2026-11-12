<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('job_hrms', function (Blueprint $table) {
            // CTC columns ko decimal me change karna
            $table->decimal('ctc_from', 20, 2)->change();
            $table->decimal('ctc_to', 20, 2)->change();
        });
    }

    public function down(): void
    {
        Schema::table('job_hrms', function (Blueprint $table) {
            // Agar rollback karna ho to phir se integer me revert
            $table->integer('ctc_from')->change();
            $table->integer('ctc_to')->change();
        });
    }
};
