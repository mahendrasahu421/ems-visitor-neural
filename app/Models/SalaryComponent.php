<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryComponent extends Model
{
    use HasFactory;

    protected $table = 'salary_components';

    protected $fillable = [
        'component_name',
        'component_code',
        'designation_id',
        'state_id',
        'city_id',
        'type',
        'calculation_type',
        'percentage',
        'fixed_amount',
        'is_taxable',
        'is_statutory',
        'status',
        'description',
    ];

    protected $casts = [
        'designation_id' => 'integer',
        'state_id'       => 'integer',
        'city_id'        => 'integer',
        'percentage'     => 'decimal:2',
        'fixed_amount'   => 'decimal:2',
        'is_taxable'     => 'boolean',
        'is_statutory'   => 'boolean',
    ];

    public function designation()
    {
        return $this->belongsTo(DesignationMaster::class, 'designation_id', 'DesignationID');
    }

    public function state()
    {
        return $this->belongsTo(CountryState::class, 'state_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo(StateCity::class, 'city_id', 'id');
    }

    public function getDesignationNameAttribute(): string
    {
        return $this->designation ? $this->designation->Designation : 'All Roles';
    }

    public function getStateNameAttribute(): string
    {
        return $this->state ? $this->state->name : 'All States';
    }

    public function getCityNameAttribute(): string
    {
        return $this->city ? $this->city->name : 'All Cities';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeEarnings($query)
    {
        return $query->where('type', 'earning');
    }

    public function scopeDeductions($query)
    {
        return $query->where('type', 'deduction');
    }
}
