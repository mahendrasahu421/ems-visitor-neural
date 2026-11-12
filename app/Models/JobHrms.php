<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobHrms extends Model
{
    protected $table = 'job_hrms';

    protected $fillable = [
        'title', 'category_id', 'job_category_id', 'skills', 'positions',
        'ctc_from', 'ctc_to', 'min_experience', 'max_experience',
        'locations', 'description', 'keywords', 'status'
    ];

    protected $casts = [
        'skills' => 'array',
        'locations' => 'array',
    ];

    // Functional area / category
    public function category()
    {
        return $this->belongsTo(JobCategory::class, 'category_id');
    }

    // Job type
    public function jobCategory()
    {
        return $this->belongsTo(JobCategory::class, 'job_category_id');
    }

    // Skills (many-to-many)
    public function skillSet()
    {
        return $this->belongsToMany(Skill::class, 'job_skill', 'job_id', 'skill_id');
    }

    // Locations (many-to-many)
    public function cities()
    {
        return $this->belongsToMany(StateCity::class, 'job_locations', 'job_id', 'city_id');
    }
}
