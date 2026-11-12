<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollBatch extends Model
{
    use HasFactory;

    protected $table = 'payroll_batches';

    protected $fillable = [
        'batch_number',
        'month',
        'year',
        'total_employees',
        'total_gross',
        'total_deductions',
        'total_net_pay',
        'status',
        'payment_date',
        'payment_mode',
        'transaction_reference',
        'processed_by',
        'approved_by',
        'notes',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'total_employees' => 'integer',
        'total_gross' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_net_pay' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function payslips()
    {
        return $this->hasMany(Payslip::class, 'payroll_batch_id');
    }

    public function processor()
    {
        return $this->belongsTo(Admin::class, 'processed_by');
    }

    public function approver()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }
}
