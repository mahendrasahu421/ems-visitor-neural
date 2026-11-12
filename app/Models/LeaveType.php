<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $table = 'leave_types';

    protected $fillable = [
        'company_id',
        'agency_id',
        'leave_name',
        'leave_code',
        'total_leaves',
        'carry_forward',
        'encashable',
        'applicable_for',
        'leave_category',
        'status',
        'description',
    ];

    protected $appends = [
        'name',
        'type',
        'days',
    ];

    public function getNameAttribute()
    {
        return $this->leave_name;
    }

    public function getTypeAttribute()
    {
        return $this->leave_category;
    }

    public function getDaysAttribute()
    {
        return $this->total_leaves;
    }

    /**
     * Scope for only active leave types.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }
    public function designations()
    {
        return $this->belongsToMany(Designation::class, 'leave_mappings', 'leave_type_id', 'designation_id');
    }
    public function leaveMappings()
    {
        return $this->hasMany(LeaveMapping::class, 'leave_type_id');
    }
}
