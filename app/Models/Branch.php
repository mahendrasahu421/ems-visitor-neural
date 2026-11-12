<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'company_id',
        'branch_name',
        'branch_code',
        'branch_owner_name',
        'contact_number',
        'gst_number',
        'country',
        'state',
        'city',
        'address_1',
        'address_2',
        'status',
    ];
}
