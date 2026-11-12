<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveActivityLog extends Model
{
    use HasFactory;

    protected $table = 'leave_activity_logs';

    public $timestamps = false;

    protected $fillable = [
        'leave_id',
        'actor_type',
        'actor_id',
        'actor_name',
        'action',
        'remarks',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function leave()
    {
        return $this->belongsTo(Leave::class, 'leave_id');
    }
}
