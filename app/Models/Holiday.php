<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = [
        'holiday_name',
        'holiday_date',
        'holiday_remark',
        'status'
    ];

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'modification_date';
    protected $dates = ['holiday_date', 'creation_date', 'modification_date'];
}
