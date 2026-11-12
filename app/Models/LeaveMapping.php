<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveMapping extends Model
{
    protected $table = 'leave_mappings';
    protected $fillable = [
        'designation_id',
        'leave_type_id',
        'allow_days',
        'status',
    ];
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }
}
