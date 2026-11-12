<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeOtp extends Model
{
    protected $table = 'employee_otps';

    protected $fillable = [
        'employee_id',
        'identifier',
        'otp_code',
        'purpose',
        'expires_at',
        'is_used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function isValid(): bool
    {
        return !$this->is_used && $this->expires_at->isFuture();
    }
}
