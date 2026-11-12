<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignationSalaryStructure extends Model
{
    use HasFactory;

    protected $table = 'designation_salary_structures';

    protected $fillable = [
        'designation_id',
        'monthly_gross',
        'basic_salary_pct',
        'hra_pct',
        'conveyance_allowance',
        'medical_allowance',
        'bike_allowance',
        'special_allowance',
        'other_allowance',
        'is_pf_enabled',
        'is_esi_enabled',
        'is_pt_enabled',
        'is_gratuity_enabled',
        'apply_state_pt_rule',
        'apply_metro_hra_rule',
        'status',
        'remarks',
    ];

    protected $casts = [
        'monthly_gross' => 'decimal:2',
        'basic_salary_pct' => 'decimal:2',
        'hra_pct' => 'decimal:2',
        'conveyance_allowance' => 'decimal:2',
        'medical_allowance' => 'decimal:2',
        'bike_allowance' => 'decimal:2',
        'special_allowance' => 'decimal:2',
        'other_allowance' => 'decimal:2',
        'is_pf_enabled' => 'boolean',
        'is_esi_enabled' => 'boolean',
        'is_pt_enabled' => 'boolean',
        'is_gratuity_enabled' => 'boolean',
        'apply_state_pt_rule' => 'boolean',
        'apply_metro_hra_rule' => 'boolean',
    ];

    public function designation()
    {
        return $this->belongsTo(DesignationMaster::class, 'designation_id', 'DesignationID');
    }
}
