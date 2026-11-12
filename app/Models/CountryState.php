<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryState extends Model
{
    protected $table = 'country_states';

    protected $fillable = [
        'name',
        'code',
        'hindi_name',
        'status',
        'country_id',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function cities()
    {
        return $this->hasMany(StateCity::class, 'state_id', 'id');
    }
}
