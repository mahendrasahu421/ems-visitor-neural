<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Project extends Model
{
    protected $table = 'client_projects';
    protected $primaryKey = 'project_id';
    public $timestamps = false;

    protected $fillable = [
        'client_id',
        'project_name',
        'engagement_type',
        'has_amc',
        'payment_mode',
        'total_value',
        'tax_percent',
        'tax_amount',
        'cgst_percent',
        'cgst_amount',
        'sgst_percent',
        'sgst_amount',
        'igst_percent',
        'igst_amount',
        'tax_type',
        'total_value_with_tax',
        'total_with_tax',
        'final_amount',
        'amc_start',
        'amc_end',
        'project_start_date',
        'project_end_date',
        'financial_year',
        'amc_working_days',
        'project_duration_days',
        'duration_mode',
        'work_type',
        'work_type_id',
        'proposal_file',
        'notes',
        'status',
        'DelStatus',
    ];

    public function getIdAttribute()
    {
        return $this->attributes['id'] ?? $this->attributes['project_id'] ?? null;
    }

    public function getClientNameAttribute($value)
    {
        if ($value !== null) {
            return $value;
        }

        if (empty($this->attributes['client_id']) || !Schema::hasTable('clientmaster')) {
            return '';
        }

        return (string) DB::table('clientmaster')
            ->where('ClientID', $this->attributes['client_id'])
            ->value('ClientName');
    }

    public function getProjectCategoryAttribute($value)
    {
        if ($value !== null) {
            return $value;
        }

        return $this->attributes['WorkTypeName']
            ?? $this->attributes['work_type_display']
            ?? $this->attributes['work_type']
            ?? $this->attributes['engagement_type']
            ?? '';
    }

    public function getStatusAttribute($value)
    {
        if ($value === null && array_key_exists('DelStatus', $this->attributes)) {
            $value = $this->attributes['DelStatus'];
        }

        $value = strtolower((string) $value);

        return $value === '1' || $value === 'active' ? 'active' : 'inactive';
    }

    public function setStatusAttribute($value): void
    {
        $column = Schema::hasColumn($this->getTable(), 'status')
            ? 'status'
            : (Schema::hasColumn($this->getTable(), 'DelStatus') ? 'DelStatus' : 'status');
        $value = strtolower((string) $value);

        $this->attributes[$column] = $value === 'active' || $value === '1' ? 1 : 0;
    }

    public function scopeActive($query)
    {
        if (Schema::hasColumn($this->getTable(), 'status')) {
            return $query->where($this->getTable() . '.status', 1);
        }

        if (Schema::hasColumn($this->getTable(), 'DelStatus')) {
            return $query->where($this->getTable() . '.DelStatus', 1);
        }

        return $query;
    }

    public function tickets()
    {
        return $this->hasMany(ProjectTicket::class, 'project_id', 'project_id');
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'project_employee_mappings', 'project_id', 'employee_id')
            ->withPivot('project_role', 'allocation_percent', 'start_date', 'end_date', 'status')
            ->withTimestamps();
    }
}
