<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('project_tickets')) {
            $this->normalizeExistingTable();

            return;
        }

        Schema::create('project_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_no')->unique();
            $table->integer('client_id');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->string('project_name')->nullable();
            $table->string('subject');
            $table->enum('priority', ['Low', 'Medium', 'High', 'Critical'])->default('Medium');
            $table->enum('status', ['Open', 'In Progress', 'Resolved', 'Closed'])->default('Open');
            $table->text('description');
            $table->string('attachment_path')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index('client_id');
            $table->index('project_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_tickets');
    }

    private function normalizeExistingTable(): void
    {
        try {
            DB::statement('ALTER TABLE project_tickets MODIFY client_id INT NOT NULL');
        } catch (\Throwable $e) {
            //
        }

        try {
            DB::statement('CREATE INDEX project_tickets_client_id_index ON project_tickets (client_id)');
        } catch (\Throwable $e) {
            //
        }

        try {
            DB::statement('CREATE INDEX project_tickets_project_id_index ON project_tickets (project_id)');
        } catch (\Throwable $e) {
            //
        }
    }
};
