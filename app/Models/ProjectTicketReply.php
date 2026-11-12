<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTicketReply extends Model
{
    protected $fillable = [
        'project_ticket_id',
        'employee_id',
        'status',
        'note',
        'work_minutes',
    ];

    public function ticket()
    {
        return $this->belongsTo(ProjectTicket::class, 'project_ticket_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function formattedWorkTime(): string
    {
        return ProjectTicket::formatMinutes((int) $this->work_minutes);
    }
}
