<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taxation extends Model
{
    use HasFactory;

    protected $table = 'taxations';

    protected $fillable = [
        'tax_name',
        'tax_code',
        'type',
        'applicable_on',
        'calculation_basis',
        'rate',
        'fixed_amount',
        'threshold_limit',
        'effective_from',
        'status',
        'description',
    ];

    protected $casts = [
        'rate'            => 'decimal:2',
        'fixed_amount'    => 'decimal:2',
        'threshold_limit' => 'decimal:2',
        'effective_from'  => 'date',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeDeductions($query)
    {
        return $query->where('type', 'deduction');
    }

    public function scopeEmployerContributions($query)
    {
        return $query->where('type', 'employer_contribution');
    }
}
