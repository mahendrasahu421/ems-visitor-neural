<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StateCity extends Model
{
    protected $table = 'state_cities';

    protected $fillable = [
        'name',
        'code',
        'hindi_name',
        'state_id',
    ];

    public function state()
    {
        return $this->belongsTo(CountryState::class, 'state_id', 'id');
    }

    public function jobs()
    {
        return $this->belongsToMany(JobHrms::class, 'job_locations', 'city_id', 'job_id');
    }
}
