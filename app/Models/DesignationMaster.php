<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignationMaster extends Model
{
    use HasFactory;

    protected $table = 'designationmaster';
    protected $primaryKey = 'DesignationID';
    public $timestamps = false;

    protected $fillable = [
        'Designation',
        'CreationDate',
    ];

    /**
     * Accessor for 'name' to keep backwards-compatibility
     */
    public function getNameAttribute()
    {
        return $this->attributes['Designation'] ?? '';
    }

    /**
     * Accessor for 'id' to keep backwards-compatibility
     */
    public function getIdAttribute()
    {
        return $this->attributes['DesignationID'] ?? null;
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'designation_id', 'DesignationID');
    }
}
