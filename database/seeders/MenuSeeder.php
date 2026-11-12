<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use Spatie\Permission\Models\Permission;

class MenuSeeder extends Seeder
{
    public function run()
    {
        // -----------------------
        // 1️⃣ Create Permissions
        // -----------------------
        $viewDashboard = Permission::firstOrCreate(['name' => 'view dashboard']);
        $manageMenu = Permission::firstOrCreate(['name' => 'manage menu']);
        $manageRoles = Permission::firstOrCreate(['name' => 'manage roles']);
        $managePermissions = Permission::firstOrCreate(['name' => 'manage permissions']);
        $mapRolePermission = Permission::firstOrCreate(['name' => 'map role permission']);

        // -----------------------
        // 2️⃣ Insert Top-Level Menus
        // -----------------------
        $dashboard = Menu::create([
            'title' => 'Dashboard',
            'icon' => 'ti ti-dashboard',
            'route' => 'admin.dashboard.index',
            'parent_id' => null,
            'permission_id' => $viewDashboard->id,
            'order' => 1,
            'status' => 1
        ]);

        $userAuth = Menu::create([
            'title' => 'User Auth',
            'icon' => 'ti ti-users',
            'route' => '#',
            'parent_id' => null,
            'permission_id' => null, // Parent menu may not need permission
            'order' => 2,
            'status' => 1
        ]);

        // -----------------------
        // 3️⃣ Insert Submenus
        // -----------------------
        $menuSub = Menu::create([
            'title' => 'Menu',
            'icon' => 'ti ti-menu',
            'route' => 'user-menu',
            'parent_id' => $userAuth->id,
            'permission_id' => $manageMenu->id,
            'order' => 1,
            'status' => 1
        ]);

        $roleSub = Menu::create([
            'title' => 'Role',
            'icon' => 'ti ti-shield',
            'route' => 'roles',
            'parent_id' => $userAuth->id,
            'permission_id' => $manageRoles->id,
            'order' => 2,
            'status' => 1
        ]);

        $permissionSub = Menu::create([
            'title' => 'Permission',
            'icon' => 'ti ti-key',
            'route' => 'permissions',
            'parent_id' => $userAuth->id,
            'permission_id' => $managePermissions->id,
            'order' => 3,
            'status' => 1
        ]);

        $mapRolePermissionSub = Menu::create([
            'title' => 'Map Role Permission',
            'icon' => 'ti ti-link',
            'route' => 'map-role-permission',
            'parent_id' => $userAuth->id,
            'permission_id' => $mapRolePermission->id,
            'order' => 4,
            'status' => 1
        ]);

        $this->command->info('Dashboard and User Auth menus seeded successfully!');
    }
}
