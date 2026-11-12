<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class EmployeeResignation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'employee_resignations';

    protected $fillable = [
        'resignation_code',
        'employee_id',
        'initiated_by',
        'initiator_id',
        'supervisor_id',
        'resignation_date',
        'requested_last_working_day',
        'approved_last_working_day',
        'notice_period_days',
        'reason',
        'reason_details',
        'cc_emails',
        'status',
        'action_by_type',
        'action_by_id',
        'action_by_name',
        'action_at',
        'action_remarks',
        'decision_token',
        'document_path',
        'feedback_rating',
        'feedback_notes',
    ];

    protected $casts = [
        'resignation_date'           => 'date',
        'requested_last_working_day' => 'date',
        'approved_last_working_day'  => 'date',
        'action_at'                  => 'datetime',
        'cc_emails'                  => 'array',
        'notice_period_days'         => 'integer',
        'feedback_rating'            => 'integer',
    ];

    /**
     * Relationship: The employee resigning or being separated.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    /**
     * Relationship: The supervisor attached to this employee.
     */
    public function supervisor()
    {
        return $this->belongsTo(Employee::class, 'supervisor_id', 'employee_id');
    }

    /**
     * Generate unique sequential code (e.g. RES-202610-0001).
     */
    public static function generateCode(): string
    {
        $prefix = 'RES-' . Carbon::now()->format('Ym') . '-';
        $latest = self::where('resignation_code', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('resignation_code');

        if ($latest) {
            $num = (int) substr($latest, strrpos($latest, '-') + 1);
            $next = str_pad($num + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $next = '0001';
        }

        return $prefix . $next;
    }

    /**
     * Generate random decision token for secure email response links.
     */
    public static function generateDecisionToken(): string
    {
        return Str::random(48);
    }

    public function isPending(): bool
    {
        return in_array($this->status, ['pending', 'in_review'], true);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function effectiveLastWorkingDay(): ?Carbon
    {
        if ($this->approved_last_working_day) {
            return Carbon::parse($this->approved_last_working_day);
        }
        if ($this->requested_last_working_day) {
            return Carbon::parse($this->requested_last_working_day);
        }
        return null;
    }

    public function daysRemaining(): ?int
    {
        $lwd = $this->effectiveLastWorkingDay();
        if (!$lwd) return null;
        $today = Carbon::today();
        return (int) $today->diffInDays($lwd, false);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 rounded-pill fs-12 fw-semibold"><i class="ti ti-clock-pause me-1"></i>Pending Review</span>',
            'in_review' => '<span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1 rounded-pill fs-12 fw-semibold"><i class="ti ti-eye me-1"></i>In Discussion</span>',
            'approved' => '<span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill fs-12 fw-semibold"><i class="ti ti-circle-check me-1"></i>Approved (Serving Notice)</span>',
            'rejected' => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill fs-12 fw-semibold"><i class="ti ti-circle-x me-1"></i>Rejected</span>',
            'withdrawn' => '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1 rounded-pill fs-12 fw-semibold"><i class="ti ti-arrow-back-up me-1"></i>Withdrawn</span>',
            'completed' => '<span class="badge bg-dark-subtle text-dark border border-dark-subtle px-2.5 py-1 rounded-pill fs-12 fw-semibold"><i class="ti ti-user-x me-1"></i>Relieved / Completed</span>',
            default => '<span class="badge bg-light text-muted border px-2.5 py-1 rounded-pill fs-12">' . ucfirst($this->status) . '</span>',
        };
    }

    public function getInitiatedByBadgeAttribute(): string
    {
        return match ($this->initiated_by) {
            'employee' => '<span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11"><i class="ti ti-user me-0.5"></i>Employee (Self)</span>',
            'supervisor' => '<span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11"><i class="ti ti-crown me-0.5"></i>Supervisor</span>',
            'admin' => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-11"><i class="ti ti-shield me-0.5"></i>Admin / HR</span>',
            default => '<span class="badge bg-light text-muted border fs-11">' . ucfirst($this->initiated_by) . '</span>',
        };
    }

    public function getCleanCcEmailsAttribute(): array
    {
        if (empty($this->cc_emails)) {
            return [];
        }
        $emails = is_array($this->cc_emails) ? $this->cc_emails : json_decode($this->cc_emails, true);
        if (!is_array($emails)) {
            return [];
        }
        return array_values(array_filter(array_map('trim', $emails), fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL)));
    }
}
