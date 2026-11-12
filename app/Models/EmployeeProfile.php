<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'patId',
        'gender',
        'posting_state',
        'posting_city',
        'posting_district',
        'joining_date',
        'band',
        'department_id',
        'date_of_confirmation',
        'employment_type',
        'retirement_date',
        'designation_id',
        'category_id',
        'offline_review',
        'pmp_type',
        'last_year_pli',
        'next_year_pli',
        'increment_last_yr',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
