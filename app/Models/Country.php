<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'countries';

    // Mass assignable fields
    protected $fillable = [
        'code',
        'name',
        'phonecode',
    ];

    // Laravel timestamps are enabled by default.
    // This line is optional when the table has created_at and updated_at columns.
    public $timestamps = true;
}
