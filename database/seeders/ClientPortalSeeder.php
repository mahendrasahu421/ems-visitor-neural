<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ClientPortalSeeder extends Seeder
{
    public function run(): void
    {
        $clientRole = Role::firstOrCreate([
            'name' => 'Client',
            'guard_name' => 'client',
        ]);

        $adminRole = Role::where('name', 'Admin')->where('guard_name', 'web')->first()
            ?? Role::where('name', 'admin')->where('guard_name', 'web')->first();

        Permission::firstOrCreate(['name' => 'client.dashboard.view', 'guard_name' => 'client']);
        Permission::firstOrCreate(['name' => 'client.tickets.manage', 'guard_name' => 'client']);
        Permission::firstOrCreate(['name' => 'admin.client.tickets.manage', 'guard_name' => 'web']);

        if (!Schema::hasTable('menus')) {
            return;
        }

        $dashboard = Menu::firstOrCreate(
            ['route' => 'client.dashboard'],
            [
                'title' => 'Dashboard',
                'icon' => 'ti ti-layout-dashboard',
                'parent_id' => null,
                'permission_id' => null,
                'order' => 1,
                'status' => 1,
            ]
        );

        $tickets = Menu::firstOrCreate(
            ['route' => 'client.tickets.index'],
            [
                'title' => 'Project Tickets',
                'icon' => 'ti ti-ticket',
                'parent_id' => null,
                'permission_id' => null,
                'order' => 2,
                'status' => 1,
            ]
        );

        DB::table('role_menu')->updateOrInsert(
            ['role_id' => $clientRole->id, 'menu_id' => $dashboard->id],
            ['can_view' => 1, 'can_edit' => 0, 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('role_menu')->updateOrInsert(
            ['role_id' => $clientRole->id, 'menu_id' => $tickets->id],
            ['can_view' => 1, 'can_edit' => 1, 'created_at' => now(), 'updated_at' => now()]
        );

        if ($adminRole) {
            $adminTickets = Menu::updateOrCreate(
                ['route' => 'admin.tickets.index'],
                [
                    'title' => 'Project Issue Tickets',
                    'icon' => 'ti ti-ticket',
                    'parent_id' => null,
                    'permission_id' => null,
                    'order' => 40,
                    'status' => 1,
                ]
            );

            DB::table('role_menu')->updateOrInsert(
                ['role_id' => $adminRole->id, 'menu_id' => $adminTickets->id],
                ['can_view' => 1, 'can_edit' => 1, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        if (Schema::hasColumn('clients', 'role_id')) {
            DB::table('clients')->whereNull('role_id')->update(['role_id' => $clientRole->id]);
        }
    }
}
