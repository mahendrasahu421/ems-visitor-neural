<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agency extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'agencies';

    protected $fillable = [
        'agency_name',
        'agency_code',
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relationship: An agency has many employees.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class, 'agency_id', 'id');
    }

    /**
     * Scope for active agencies.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}
