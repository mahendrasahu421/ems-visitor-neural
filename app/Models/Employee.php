<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $table = 'employees';
    protected $primaryKey = 'employee_id';
    public $timestamps = false;

    protected $fillable = [
        'employee_name',
        'employee_email',
        'employee_mobile',
        'employee_password',
        'login_pin',
        'is_pin_enabled',
        'pin_set_at',
        'last_login_at',
        'employee_gender',
        'blood_group',
        'posting_country',
        'posting_state',
        'posting_city',
        'joining_date',
        'date_of_confirmation',
        'employment_type',
        'designation_id',
        'agency_id',
        'shift_id',
        'week_off_days',
        'supervisor_id',
        'is_supervisor',
        'status',
        'profile_image',
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'is_supervisor' => 'boolean',
        'is_pin_enabled' => 'boolean',
        'pin_set_at' => 'datetime',
        'last_login_at' => 'datetime',
        'week_off_days' => 'array',
    ];

    protected $hidden = [
        'employee_password',
        'login_pin',
    ];

    protected static function booted(): void
    {
        static::saved(function (Employee $employee) {
            if ($employee->isDirty('status') && !$employee->isActive()) {
                if (method_exists($employee, 'tokens')) {
                    $employee->tokens()->delete();
                }
                SalaryStructure::where('employee_id', $employee->employee_id)
                    ->update(['status' => 'Inactive']);
                EmployeeOtp::where('employee_id', $employee->employee_id)
                    ->where('is_used', false)
                    ->update(['is_used' => true]);
            }
        });

        static::deleted(function (Employee $employee) {
            if (method_exists($employee, 'tokens')) {
                $employee->tokens()->delete();
            }
            SalaryStructure::where('employee_id', $employee->employee_id)
                ->update(['status' => 'Inactive']);
            EmployeeOtp::where('employee_id', $employee->employee_id)
                ->where('is_used', false)
                ->update(['is_used' => true]);
        });
    }

    public function getAuthPassword()
    {
        return $this->employee_password;
    }

    /**
     * Determine if the employee is currently active and eligible for work, login, and payroll.
     */
    public function isActive(): bool
    {
        if ($this->deleted_at !== null) {
            return false;
        }

        $status = strtolower(trim((string) $this->status));

        if (in_array($status, ['inactive', '0', 'disabled', 'suspended', 'left', 'terminated'], true)) {
            return false;
        }

        return true;
    }

    /**
     * Deactivate the employee: revoke mobile app tokens, deactivate active salary structures, invalidate active OTPs.
     */
    public function deactivate(): void
    {
        $this->status = 'Inactive';
        $this->save();

        // 1. Revoke all Mobile App API tokens (Sanctum) immediately
        if (method_exists($this, 'tokens')) {
            $this->tokens()->delete();
        }

        // 2. Deactivate any active salary structures
        SalaryStructure::where('employee_id', $this->employee_id)
            ->update(['status' => 'Inactive']);

        // 3. Invalidate active OTPs
        EmployeeOtp::where('employee_id', $this->employee_id)
            ->where('is_used', false)
            ->update(['is_used' => true]);
    }

    /**
     * Activate the employee.
     */
    public function activate(): void
    {
        $this->status = 'Active';
        $this->save();
    }

    /**
     * Query scope for active employees only.
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at')
            ->where(function ($q) {
                $q->where('status', 'Active')
                  ->orWhere('status', '1')
                  ->orWhereNull('status')
                  ->orWhere('status', '');
            })
            ->where('status', '!=', 'Inactive');
    }

    public function isSupervisor(): bool
    {
        if ($this->is_supervisor) {
            return true;
        }

        if (self::where('supervisor_id', $this->employee_id)->exists()) {
            return true;
        }

        if (!empty($this->employee_email)) {
            $user = User::where('email', $this->employee_email)->first();
            if ($user && ($user->hasRole('Supervisor') || $user->hasRole('Admin') || $user->hasRole('Super Admin') || $user->role_id == 6)) {
                return true;
            }
        }

        return false;
    }

    public function subordinates()
    {
        return $this->hasMany(Employee::class, 'supervisor_id', 'employee_id');
    }

    public function teamQuery()
    {
        $hasExplicit = self::where('supervisor_id', $this->employee_id)->exists();

        if ($hasExplicit) {
            return self::where('supervisor_id', $this->employee_id)->whereNull('deleted_at');
        }

        return self::where('employee_id', '!=', $this->employee_id)
            ->whereNull('deleted_at')
            ->where(function ($q) {
                $q->where('status', 'Active')
                  ->orWhere('status', '1')
                  ->orWhereNull('status')
                  ->orWhere('status', '');
            });
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id', 'id');
    }

    public function supervisorEmployee()
    {
        return $this->belongsTo(Employee::class, 'supervisor_id', 'employee_id');
    }

    public function country()
    {
        return $this->belongsTo(\App\Models\Country::class, 'posting_country', 'id');
    }

    public function state()
    {
        return $this->belongsTo(\App\Models\CountryState::class, 'posting_state', 'id');
    }

    public function city()
    {
        return $this->belongsTo(\App\Models\StateCity::class, 'posting_city', 'id');
    }

    public function designation()
    {
        return $this->belongsTo(\App\Models\Designation::class, 'designation_id', 'id')
            ->withDefault([
                'Designation' => 'Staff Member',
                'name' => 'Staff Member',
            ]);
    }

    public function agency()
    {
        return $this->belongsTo(\App\Models\Agency::class, 'agency_id', 'id');
    }

    public function shift()
    {
        return $this->belongsTo(\App\Models\Shift::class, 'shift_id', 'id');
    }

    public function projects()
    {
        if (\Illuminate\Support\Facades\Schema::hasTable('project_employee_mappings')) {
            return $this->belongsToMany(\App\Models\Project::class, 'project_employee_mappings', 'employee_id', 'project_id')
                ->withPivot('project_role', 'allocation_percent', 'start_date', 'end_date', 'status')
                ->withTimestamps();
        }

        return $this->hasMany(\App\Models\EmployeeAttendance::class, 'employee_id', 'employee_id')->whereRaw('1 = 0');
    }

    public function attendances()
    {
        return $this->hasMany(\App\Models\EmployeeAttendance::class, 'employee_id', 'employee_id');
    }

    public function leaves()
    {
        return $this->hasMany(\App\Models\Leave::class, 'employee_id', 'employee_id');
    }

    public function otps()
    {
        return $this->hasMany(\App\Models\EmployeeOtp::class, 'employee_id', 'employee_id');
    }

    public function authLogs()
    {
        return $this->hasMany(\App\Models\EmployeeAuthActivityLog::class, 'employee_id', 'employee_id')->orderBy('created_at', 'desc');
    }

    public function salaryStructure()
    {
        return $this->hasOne(\App\Models\SalaryStructure::class, 'employee_id', 'employee_id')
            ->where('status', 'Active')
            ->latest('effective_from');
    }

    public function salaryStructures()
    {
        return $this->hasMany(\App\Models\SalaryStructure::class, 'employee_id', 'employee_id')
            ->orderBy('effective_from', 'desc');
    }

    public function payslips()
    {
        return $this->hasMany(\App\Models\Payslip::class, 'employee_id', 'employee_id')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc');
    }

    public function documents()
    {
        return $this->hasMany(\App\Models\EmployeeDocument::class, 'employee_id', 'employee_id')
            ->orderBy('created_at', 'desc');
    }

    public function increments()
    {
        return $this->hasMany(\App\Models\EmployeeIncrement::class, 'employee_id', 'employee_id')
            ->orderBy('effective_date', 'desc')
            ->orderBy('id', 'desc');
    }

    public function latestIncrement()
    {
        return $this->hasOne(\App\Models\EmployeeIncrement::class, 'employee_id', 'employee_id')
            ->where('status', 'Applied')
            ->latestOfMany('effective_date');
    }

    public function resignations()
    {
        return $this->hasMany(\App\Models\EmployeeResignation::class, 'employee_id', 'employee_id')
            ->orderBy('id', 'desc');
    }

    public function latestResignation()
    {
        return $this->hasOne(\App\Models\EmployeeResignation::class, 'employee_id', 'employee_id')
            ->latestOfMany();
    }

    public function activeResignation()
    {
        return $this->hasOne(\App\Models\EmployeeResignation::class, 'employee_id', 'employee_id')
            ->whereIn('status', ['pending', 'in_review', 'approved'])
            ->latestOfMany();
    }
}


