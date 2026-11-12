<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('project_ticket_replies')) {
            return;
        }

        Schema::create('project_ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_ticket_id');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->string('status', 50)->nullable();
            $table->text('note')->nullable();
            $table->unsignedInteger('work_minutes')->default(0);
            $table->timestamps();

            $table->index('project_ticket_id');
            $table->index('employee_id');
        });

        if (Schema::hasTable('project_tickets') && Schema::hasColumn('project_tickets', 'employee_note')) {
            DB::table('project_tickets')
                ->whereNotNull('employee_note')
                ->orderBy('id')
                ->chunk(100, function ($tickets) {
                    foreach ($tickets as $ticket) {
                        DB::table('project_ticket_replies')->insert([
                            'project_ticket_id' => $ticket->id,
                            'employee_id' => $ticket->assigned_employee_id ?? null,
                            'status' => $ticket->employee_status ?? null,
                            'note' => $ticket->employee_note,
                            'work_minutes' => 0,
                            'created_at' => $ticket->employee_responded_at ?? $ticket->updated_at ?? now(),
                            'updated_at' => $ticket->employee_responded_at ?? $ticket->updated_at ?? now(),
                        ]);
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('project_ticket_replies');
    }
};
