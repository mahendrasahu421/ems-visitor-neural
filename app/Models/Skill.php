<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Skill extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'slug'];

     public function jobs()
    {
        return $this->belongsToMany(JobHrms::class, 'job_skill', 'skill_id', 'job_id');
    }
}
