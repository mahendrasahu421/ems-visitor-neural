<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    use HasFactory;

    protected $table = 'push_notifications';

    protected $fillable = [
        'title',
        'body',
        'image_url',
        'action_url',
        'data_payload',
        'sender_type',
        'sender_id',
        'target_type',
        'target_id',
        'employee_id',
        'user_id',
        'is_read',
        'read_at',
        'status',
        'delivery_response',
        'error_message',
    ];

    protected $casts = [
        'data_payload' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
