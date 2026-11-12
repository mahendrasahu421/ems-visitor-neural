<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('menus')) {
            return;
        }

        $now = now();

        $parent = DB::table('menus')
            ->whereNull('parent_id')
            ->where('title', 'Attendance')
            ->first();

        if ($parent) {
            $parentId = $parent->id;
        } else {
            $parentId = DB::table('menus')->insertGetId([
                'title' => 'Attendance',
                'icon' => 'ti ti-clock-hour-4',
                'route' => null,
                'parent_id' => null,
                'permission_id' => null,
                'order' => (int) DB::table('menus')->whereNull('parent_id')->max('order') + 1,
                'status' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $route = 'attendance.attendance-dashboard.org-reports';

        $menu = DB::table('menus')->where('route', $route)->first();

        if (!$menu) {
            $menuId = DB::table('menus')->insertGetId([
                'title' => 'Check In/Out Report',
                'icon' => 'ti ti-login-2',
                'route' => $route,
                'parent_id' => $parentId,
                'permission_id' => null,
                'order' => (int) DB::table('menus')->where('parent_id', $parentId)->max('order') + 1,
                'status' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $menuId = $menu->id;

            DB::table('menus')
                ->where('id', $menuId)
                ->update([
                    'title' => 'Check In/Out Report',
                    'parent_id' => $parentId,
                    'status' => true,
                    'updated_at' => $now,
                ]);
        }

        if (!Schema::hasTable('role_menu') || !Schema::hasTable('roles')) {
            return;
        }

        $roleIds = DB::table('roles')
            ->whereIn('id', [1, 2])
            ->pluck('id');

        foreach ($roleIds as $roleId) {
            $exists = DB::table('role_menu')
                ->where('role_id', $roleId)
                ->where('menu_id', $menuId)
                ->exists();

            if (!$exists) {
                DB::table('role_menu')->insert([
                    'role_id' => $roleId,
                    'menu_id' => $menuId,
                    'can_view' => true,
                    'can_edit' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('menus')) {
            return;
        }

        $route = 'attendance.attendance-dashboard.org-reports';
        $menuId = DB::table('menus')->where('route', $route)->value('id');

        if ($menuId && Schema::hasTable('role_menu')) {
            DB::table('role_menu')->where('menu_id', $menuId)->delete();
        }

        DB::table('menus')->where('route', $route)->delete();
    }
};
