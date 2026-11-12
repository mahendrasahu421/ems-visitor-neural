<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Client extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'role_id',
        'invoice_client_id',
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'phone',
        'company_name',
        'profile',
        'status',
        'invoiceDate',
        'client_name',
        'concern_person',
        'cin',
        'website',
        'mobile',
        'country_id',
        'state_id',
        'address',
        'gst_number',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function getFullNameAttribute(): string
    {
        $name = trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));

        return $name !== '' ? $name : ($this->client_name ?? '');
    }

    public function role()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class, 'role_id');
    }

    public function tickets()
    {
        return $this->hasMany(ProjectTicket::class);
    }

    public function matchingProjects()
    {
        $clientId = $this->id;
        $invoiceClientId = $this->invoice_client_id;
        $email = $this->email;

        $names = collect([
            $this->company_name,
            $this->full_name,
            $this->username,
            $this->email,
        ])->filter()->unique()->values();

        return Project::query()
            ->leftJoin('clientmaster as c', 'client_projects.client_id', '=', 'c.ClientID')
            ->select('client_projects.*', 'c.ClientName as client_name', 'client_projects.work_type as project_category')
            ->where(function ($query) use ($clientId, $invoiceClientId, $email, $names) {
                if ($invoiceClientId) {
                    $query->orWhere('client_projects.client_id', $invoiceClientId);
                }

                if (\Illuminate\Support\Facades\Schema::hasTable('project_concern_persons')) {
                    $query->orWhereExists(function ($sub) use ($clientId, $email) {
                        $sub->select(\Illuminate\Support\Facades\DB::raw(1))
                            ->from('project_concern_persons as pcp')
                            ->whereColumn('pcp.project_id', 'client_projects.project_id')
                            ->where(function ($w) use ($clientId, $email) {
                                $w->where('pcp.login_client_id', $clientId);
                                if ($email) {
                                    $w->orWhere('pcp.email', $email);
                                }
                            });
                    });
                }

                if ($names->isNotEmpty()) {
                    $query->orWhere(function ($innerQuery) use ($names) {
                        foreach ($names as $name) {
                            $innerQuery->orWhere('c.ClientName', 'like', '%' . $name . '%');
                        }
                    });
                }
            })
            ->distinct();
    }
}
