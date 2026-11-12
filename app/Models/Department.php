<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'company_id',
        'category_id',
        'department_name',
        'department_code',
        'department_head',
        'status',
    ];

    public function category()
{
    return $this->belongsTo(Category::class, 'category_id');
}

}
