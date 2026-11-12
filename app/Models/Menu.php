<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'title',
        'icon',
        'route',
        'parent_id',
        'permission_id',
        'order',
        'status'
    ];
    public function getRouteNameAttribute()
    {
        return $this->route; // 'route' column ko Blade me route_name se access kar rahe ho
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order', 'asc');
    }

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function permission()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Permission::class, 'permission_id');
    }
    // App\Models\Menu.php
    public function roles()
    {
        return $this->belongsToMany(\Spatie\Permission\Models\Role::class, 'role_menu', 'menu_id', 'role_id')
            ->withPivot('can_view', 'can_edit')
            ->withTimestamps();
    }

}
