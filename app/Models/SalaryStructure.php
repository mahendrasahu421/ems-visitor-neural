<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryStructure extends Model
{
    use HasFactory;

    protected $table = 'salary_structures';

    protected $fillable = [
        'employee_id',
        'effective_from',
        'ctc_annual',
        'monthly_gross',
        'basic_salary',
        'hra',
        'conveyance_allowance',
        'medical_allowance',
        'special_allowance',
        'other_allowance',
        'bike_allowance',
        'gratuity',
        'pf_employee',
        'pf_employer',
        'esi_employee',
        'esi_employer',
        'professional_tax',
        'tds_monthly',
        'bank_name',
        'bank_account_no',
        'ifsc_code',
        'pan_no',
        'uan_no',
        'pf_no',
        'esi_no',
        'payment_mode',
        'status',
        'remarks',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'ctc_annual' => 'decimal:2',
        'monthly_gross' => 'decimal:2',
        'basic_salary' => 'decimal:2',
        'hra' => 'decimal:2',
        'conveyance_allowance' => 'decimal:2',
        'medical_allowance' => 'decimal:2',
        'special_allowance' => 'decimal:2',
        'other_allowance' => 'decimal:2',
        'bike_allowance' => 'decimal:2',
        'gratuity' => 'decimal:2',
        'pf_employee' => 'decimal:2',
        'pf_employer' => 'decimal:2',
        'esi_employee' => 'decimal:2',
        'esi_employer' => 'decimal:2',
        'professional_tax' => 'decimal:2',
        'tds_monthly' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
