<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    protected $table = 'leaves';

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'holiday_id',
        'from_date',
        'to_date',
        'duration',
        'reason',
        'document_path',
        'status',
        'approved_by',
        'rejected_by',
        'supervisor_remarks',
        'action_at',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'action_at' => 'datetime',
        'duration' => 'integer',
        'holiday_id' => 'integer',
    ];

    // Relationships

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id', 'id');
    }

    public function holiday()
    {
        return $this->belongsTo(Holiday::class, 'holiday_id', 'id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function rejecter()
    {
        return $this->belongsTo(User::class, 'rejected_by', 'id');
    }

    public function activityLogs()
    {
        return $this->hasMany(LeaveActivityLog::class, 'leave_id', 'id')->orderBy('created_at', 'desc');
    }
}
