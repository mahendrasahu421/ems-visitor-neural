<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    use HasFactory;

    protected $table = 'employee_documents';

    protected $fillable = [
        'employee_id',
        'document_type',
        'document_name',
        'document_number',
        'file_path',
        'file_name',
        'file_size',
        'file_extension',
        'issue_date',
        'expiry_date',
        'verification_status',
        'rejection_reason',
        'verified_by',
        'verified_at',
        'notes',
    ];

    protected $casts = [
        'issue_date'   => 'date',
        'expiry_date'  => 'date',
        'verified_at'  => 'datetime',
        'file_size'    => 'integer',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by', 'id');
    }

    public function scopeApproved($query)
    {
        return $query->where('verification_status', 'Approved');
    }

    public function scopePending($query)
    {
        return $query->where('verification_status', 'Pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('verification_status', 'Rejected');
    }

    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 0) . ' KB';
        }
        return $bytes . ' B';
    }

    public function getFileUrlAttribute(): string
    {
        if ($this->file_path && file_exists(public_path($this->file_path))) {
            return asset($this->file_path);
        }
        return asset('uploads/documents/' . $this->file_name);
    }
}
