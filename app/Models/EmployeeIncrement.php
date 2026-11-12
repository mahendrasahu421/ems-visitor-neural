<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeIncrement extends Model
{
    use HasFactory;

    protected $table = 'employee_increments';

    protected $fillable = [
        'increment_number',
        'employee_id',
        'review_cycle',
        'effective_date',
        'current_ctc',
        'current_gross',
        'increment_type',
        'increment_value',
        'hike_percentage',
        'hike_amount',
        'new_ctc',
        'new_gross',
        'previous_designation_id',
        'new_designation_id',
        'previous_salary_structure_id',
        'new_salary_structure_id',
        'performance_rating',
        'status',
        'remarks',
        'letter_notes',
        'approved_by',
        'created_by',
        'applied_at',
    ];

    protected $casts = [
        'effective_date'   => 'date',
        'applied_at'       => 'datetime',
        'current_ctc'      => 'decimal:2',
        'current_gross'    => 'decimal:2',
        'increment_value'  => 'decimal:2',
        'hike_percentage'  => 'decimal:2',
        'hike_amount'      => 'decimal:2',
        'new_ctc'          => 'decimal:2',
        'new_gross'        => 'decimal:2',
    ];

    /**
     * Relationship: The employee who received the increment.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    /**
     * Relationship: Designation prior to revision.
     */
    public function previousDesignation()
    {
        return $this->belongsTo(Designation::class, 'previous_designation_id');
    }

    /**
     * Relationship: New Designation after revision (promotion).
     */
    public function newDesignation()
    {
        return $this->belongsTo(Designation::class, 'new_designation_id');
    }

    /**
     * Relationship: Pre-revision salary structure snapshot.
     */
    public function previousSalaryStructure()
    {
        return $this->belongsTo(SalaryStructure::class, 'previous_salary_structure_id');
    }

    /**
     * Relationship: Post-revision active salary structure snapshot.
     */
    public function newSalaryStructure()
    {
        return $this->belongsTo(SalaryStructure::class, 'new_salary_structure_id');
    }

    /**
     * Relationship: Administrator who approved the increment.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if this increment included a designation promotion.
     */
    public function isPromotion(): bool
    {
        return !empty($this->new_designation_id) && ($this->new_designation_id !== $this->previous_designation_id);
    }

    /**
     * Format status badge CSS class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'Applied', 'Approved' => 'badge-soft-success text-success border border-success-subtle',
            'Draft'               => 'badge-soft-warning text-warning border border-warning-subtle',
            'Rejected'            => 'badge-soft-danger text-danger border border-danger-subtle',
            default               => 'badge-soft-secondary text-secondary',
        };
    }
}
