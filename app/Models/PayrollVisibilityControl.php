<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollVisibilityControl extends Model
{
    use HasFactory;

    protected $table = 'payroll_visibility_controls';

    protected $fillable = [
        'rule_name',
        'rule_code',
        'applies_to',
        'access_level',
        'can_view_salary',
        'can_edit_structure',
        'can_process_payroll',
        'can_download_reports',
        'mask_sensitive_components',
        'status',
        'description',
    ];

    protected $casts = [
        'can_view_salary'           => 'boolean',
        'can_edit_structure'        => 'boolean',
        'can_process_payroll'       => 'boolean',
        'can_download_reports'      => 'boolean',
        'mask_sensitive_components' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeByRole($query, string $role)
    {
        return $query->where('applies_to', $role);
    }
}
