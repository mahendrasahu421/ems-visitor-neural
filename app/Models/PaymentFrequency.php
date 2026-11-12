<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentFrequency extends Model
{
    use HasFactory;

    protected $table = 'payment_frequencies';

    protected $fillable = [
        'frequency_name',
        'frequency_code',
        'cycles_per_year',
        'days_per_cycle',
        'pay_day_rule',
        'is_default',
        'status',
        'description',
    ];

    protected $casts = [
        'cycles_per_year' => 'integer',
        'days_per_cycle'  => 'integer',
        'is_default'      => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}
