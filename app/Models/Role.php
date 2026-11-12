<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public function menus()
    {
        return $this->belongsToMany(\App\Models\Menu::class, 'role_menu', 'role_id', 'menu_id')
            ->withPivot('can_view', 'can_edit')
            ->withTimestamps();
    }

}
