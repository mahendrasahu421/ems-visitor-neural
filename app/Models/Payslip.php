<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payslip extends Model
{
    use HasFactory;

    protected $table = 'payslips';

    protected $fillable = [
        'payslip_number',
        'payroll_batch_id',
        'employee_id',
        'month',
        'year',
        'total_days',
        'present_days',
        'paid_leave_days',
        'holiday_days',
        'weekly_off_days',
        'lwp_days',
        'payable_days',
        'base_gross',
        'earned_basic',
        'earned_hra',
        'earned_conveyance',
        'earned_medical',
        'earned_special_allowance',
        'earned_bike_allowance',
        'bonus',
        'incentive',
        'overtime_pay',
        'total_earnings',
        'pf_deduction',
        'esi_deduction',
        'pt_deduction',
        'tds_deduction',
        'lwp_deduction',
        'other_deductions',
        'gratuity_accrual',
        'total_deductions',
        'net_salary',
        'net_salary_in_words',
        'bank_name',
        'bank_account_no',
        'ifsc_code',
        'pan_no',
        'uan_no',
        'designation_name',
        'department_name',
        'payment_status',
        'payment_date',
        'payment_mode',
        'transaction_reference',
        'pdf_path',
        'remarks',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'total_days' => 'integer',
        'present_days' => 'decimal:1',
        'paid_leave_days' => 'decimal:1',
        'holiday_days' => 'decimal:1',
        'weekly_off_days' => 'decimal:1',
        'lwp_days' => 'decimal:1',
        'payable_days' => 'decimal:1',
        'base_gross' => 'decimal:2',
        'earned_basic' => 'decimal:2',
        'earned_hra' => 'decimal:2',
        'earned_conveyance' => 'decimal:2',
        'earned_medical' => 'decimal:2',
        'earned_special_allowance' => 'decimal:2',
        'earned_bike_allowance' => 'decimal:2',
        'bonus' => 'decimal:2',
        'incentive' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'pf_deduction' => 'decimal:2',
        'esi_deduction' => 'decimal:2',
        'pt_deduction' => 'decimal:2',
        'tds_deduction' => 'decimal:2',
        'lwp_deduction' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'gratuity_accrual' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function batch()
    {
        return $this->belongsTo(PayrollBatch::class, 'payroll_batch_id');
    }

    public function getMonthNameAttribute()
    {
        return \Carbon\Carbon::createFromDate($this->year, $this->month, 1)->format('F');
    }

    public function getPeriodFormattedAttribute()
    {
        return \Carbon\Carbon::createFromDate($this->year, $this->month, 1)->format('F Y');
    }
}
