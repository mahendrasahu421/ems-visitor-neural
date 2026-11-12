<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('employee_documents')) {
            Schema::create('employee_documents', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id')->index();
                $table->string('document_type', 100);
                $table->string('document_name', 191);
                $table->string('document_number', 100)->nullable();
                $table->string('file_path', 255);
                $table->string('file_name', 191);
                $table->unsignedBigInteger('file_size')->default(0);
                $table->string('file_extension', 20)->default('pdf');
                $table->date('issue_date')->nullable();
                $table->date('expiry_date')->nullable();
                $table->enum('verification_status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
                $table->text('rejection_reason')->nullable();
                $table->unsignedBigInteger('verified_by')->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_documents');
    }
};
