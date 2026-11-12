<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    use HasFactory;

    protected $table = 'dailyreport';
    protected $primaryKey = 'report_ID';
    public $timestamps = false;

    protected $fillable = [
        'ProjectID',
        'EmpId',
        'WorkDescription',
        'StartingTime',
        'EndingTime',
        'TotalHours',
        'DrEntryDate',
        'CreationDate',
    ];

    protected $casts = [
        'StartingTime' => 'datetime:H:i',
        'EndingTime'   => 'datetime:H:i',
        'DrEntryDate'  => 'datetime',
        'CreationDate' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'EmpId', 'id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'ProjectID', 'project_id');
    }
    public function employeeDetails()
    {
        return $this->belongsTo(Employee::class, 'EmpId', 'employee_id');
    }

    public function projectDetails()
    {
        return $this->belongsTo(Project::class, 'ProjectID', 'project_id');
    }
}
