<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectConcernPerson extends Model
{
    use HasFactory;

    protected $table = 'project_concern_persons';

    protected $fillable = [
        'project_id',
        'client_id',
        'login_client_id',
        'name',
        'email',
        'mobile',
        'designation',
        'is_primary',
        'can_login',
        'credentials_sent_at',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'can_login' => 'boolean',
        'credentials_sent_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'invoice_client_id');
    }

    public function loginAccount()
    {
        return $this->belongsTo(Client::class, 'login_client_id', 'id');
    }
}
