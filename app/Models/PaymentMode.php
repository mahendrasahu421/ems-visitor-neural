<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMode extends Model
{
    use HasFactory;

    protected $table = 'payment_modes';

    protected $fillable = [
        'mode_name',
        'mode_code',
        'channel_type',
        'requires_bank_details',
        'processing_fee',
        'is_default',
        'status',
        'description',
    ];

    protected $casts = [
        'requires_bank_details' => 'boolean',
        'is_default'            => 'boolean',
        'processing_fee'        => 'decimal:2',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeElectronic($query)
    {
        return $query->where('channel_type', 'electronic');
    }

    public function scopePhysical($query)
    {
        return $query->where('channel_type', 'physical');
    }
}
