<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Menu;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // permissions
        Permission::firstOrCreate(['name' => 'view-branches']);
        Permission::firstOrCreate(['name' => 'manage-branches']);
        Permission::firstOrCreate(['name' => 'view-employees']);
        Permission::firstOrCreate(['name' => 'manage-employees']);
        Permission::firstOrCreate(['name' => 'view-reports']);

        // roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $manager = Role::firstOrCreate(['name' => 'branch_manager']);
        $hr = Role::firstOrCreate(['name' => 'hr']);

        // assign permissions to roles
        $admin->givePermissionTo(Permission::all()); // admin gets all
        $manager->givePermissionTo(['view-branches', 'view-employees', 'view-reports']);
        $hr->givePermissionTo(['view-employees', 'manage-employees']);

        // Example menu rows (you can also insert via GUI)
        Menu::create(['title' => 'Dashboard', 'route_name' => 'admin.dashboard.index', 'parent_id' => 0, 'icon' => 'ti-layout-dashboard', 'permission_name' => null, 'order' => 1]);
        Menu::create(['title' => 'Branch Master', 'route_name' => 'admin.branches.index', 'parent_id' => 0, 'icon' => 'ti-building', 'permission_name' => 'view-branches', 'order' => 10]);
        Menu::create(['title' => 'Employee Master', 'route_name' => 'admin.employees.index', 'parent_id' => 0, 'icon' => 'ti-users', 'permission_name' => 'view-employees', 'order' => 20]);
        Menu::create(['title' => 'Reports', 'route_name' => 'admin.reports.index', 'parent_id' => 0, 'icon' => 'ti-report', 'permission_name' => 'view-reports', 'order' => 30]);

        // Submenu example (child of Branch Master)
        $branch = Menu::where('route_name', 'admin.branches.index')->first();
        if ($branch) {
            Menu::create(['title' => 'Add Branch', 'route_name' => 'admin.branches.create', 'parent_id' => $branch->id, 'permission_name' => 'manage-branches', 'order' => 1]);
        }
    }
}
