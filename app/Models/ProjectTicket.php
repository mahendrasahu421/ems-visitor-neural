<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTicket extends Model
{
    protected $fillable = [
        'ticket_no',
        'client_id',
        'project_id',
        'assigned_employee_id',
        'project_name',
        'subject',
        'priority',
        'status',
        'employee_status',
        'description',
        'attachment_path',
        'admin_note',
        'employee_note',
        'employee_responded_at',
        'assigned_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'employee_responded_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function assignedEmployee()
    {
        return $this->belongsTo(Employee::class, 'assigned_employee_id', 'employee_id');
    }

    public function replies()
    {
        return $this->hasMany(ProjectTicketReply::class, 'project_ticket_id')->latest();
    }

    public function totalWorkMinutes(): int
    {
        if ($this->relationLoaded('replies')) {
            return (int) $this->replies->sum('work_minutes');
        }

        return (int) $this->replies()->sum('work_minutes');
    }

    public function formattedTotalWorkTime(): string
    {
        return self::formatMinutes($this->totalWorkMinutes());
    }

    public function formattedResolutionTime(): string
    {
        if (!$this->created_at || !$this->closed_at) {
            return 'Not resolved yet';
        }

        return self::formatMinutes($this->created_at->diffInMinutes($this->closed_at));
    }

    public function formattedOpenTime(): string
    {
        if (!$this->created_at) {
            return '-';
        }

        return self::formatMinutes($this->created_at->diffInMinutes($this->closed_at ?? now()));
    }

    public static function formatMinutes(int $minutes): string
    {
        if ($minutes <= 0) {
            return '0m';
        }

        $days = intdiv($minutes, 1440);
        $minutes %= 1440;
        $hours = intdiv($minutes, 60);
        $minutes %= 60;
        $parts = [];

        if ($days > 0) {
            $parts[] = $days . 'd';
        }

        if ($hours > 0) {
            $parts[] = $hours . 'h';
        }

        if ($minutes > 0 || empty($parts)) {
            $parts[] = $minutes . 'm';
        }

        return implode(' ', $parts);
    }
}
