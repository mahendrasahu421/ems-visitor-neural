<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollApplicability extends Model
{
    use HasFactory;

    protected $table = 'payroll_applicabilities';

    protected $fillable = [
        'rule_name',
        'rule_code',
        'applies_to',
        'salary_condition',
        'min_salary',
        'max_salary',
        'min_service_days',
        'effective_from',
        'status',
        'description',
    ];

    protected $casts = [
        'min_salary'       => 'decimal:2',
        'max_salary'       => 'decimal:2',
        'min_service_days' => 'integer',
        'effective_from'   => 'date',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeByAppliesTo($query, string $appliesTo)
    {
        return $query->where('applies_to', $appliesTo);
    }
}
