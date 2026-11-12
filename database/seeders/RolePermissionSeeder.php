<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $roles = [
            'Super Admin',
            'Admin',
            'HR',
            'Employee',
            'Manager',
            'Supervisor',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
        }

        $permissions = [
            // Dashboard
            'dashboard.view',

            // Access control: Admin assigns users, roles, permissions, and menus.
            'access.manage',
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
            'role.view',
            'role.create',
            'role.edit',
            'role.delete',
            'permission.view',
            'permission.create',
            'permission.edit',
            'permission.delete',
            'menu.view',
            'menu.assign',

            // Employee management
            'employee.profile.view',
            'employee.view',
            'employee.create',
            'employee.edit',
            'employee.delete',
            'employee.import',
            'employee-report.view',
            'employee-report.export',

            // Attendance
            'attendance.manage',
            'attendance.view',
            'attendance.report.view',
            'employee.attendance.checkin',
            'employee.attendance.checkout',
            'employee.attendance.view',

            // Leave and holidays
            'leave.apply',
            'leave.view',
            'leave.approve',
            'leave.reject',
            'leave.manage',
            'employee.leave.apply',
            'employee.leave.view',
            'holiday.view',
            'holiday.manage',
            'holidays.view',

            // Employee self-service
            'employee.calendar.view',
            'employee.ticket.view',
            'employee.ticket.update',
            'employee.daily-report.view',
            'employee.daily-report.create',

            // Operations
            'project.view',
            'project.manage',
            'client.view',
            'client.manage',
            'ticket.view',
            'ticket.manage',

            // HR modules
            'recruitment.view',
            'recruitment.manage',
            'onboarding.view',
            'onboarding.manage',
            'performance.view',
            'performance.manage',

            // Payroll and finance
            'payroll.view',
            'payroll.manage',
            'invoice.view',
            'invoice.manage',

            // Settings
            'settings.view',
            'settings.manage',
            'company.manage',
            'branch.manage',
            'department.manage',
            'designation.manage',

            // Supervisor permissions
            'supervisor.dashboard',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        $allWebPermissions = Permission::where('guard_name', 'web')->get();

        Role::whereIn('name', ['Super Admin', 'Admin'])
            ->where('guard_name', 'web')
            ->get()
            ->each(fn (Role $role) => $role->syncPermissions($allWebPermissions));

        Role::where('name', 'HR')->where('guard_name', 'web')->first()
            ?->syncPermissions([
                'dashboard.view',
                'employee.profile.view',
                'employee.view',
                'employee.create',
                'employee.edit',
                'employee-report.view',
                'employee-report.export',
                'attendance.manage',
                'attendance.view',
                'attendance.report.view',
                'leave.view',
                'leave.approve',
                'leave.reject',
                'leave.manage',
                'holiday.view',
                'holiday.manage',
                'recruitment.view',
                'recruitment.manage',
                'onboarding.view',
                'onboarding.manage',
                'performance.view',
                'performance.manage',
                'settings.view',
            ]);

        Role::where('name', 'Manager')->where('guard_name', 'web')->first()
            ?->syncPermissions([
                'dashboard.view',
                'employee.view',
                'employee-report.view',
                'attendance.view',
                'attendance.report.view',
                'leave.view',
                'project.view',
                'project.manage',
                'ticket.view',
                'ticket.manage',
            ]);

        Role::where('name', 'Supervisor')->where('guard_name', 'web')->first()
            ?->syncPermissions([
                'dashboard.view',
                'supervisor.dashboard',
                'employee.view',
                'employee.profile.view',
                'attendance.view',
                'attendance.report.view',
                'leave.view',
                'leave.approve',
                'leave.reject',
                'employee-report.view',
                'holiday.view',
                'holidays.view',
            ]);

        Role::where('name', 'Employee')->where('guard_name', 'web')->first()
            ?->syncPermissions([
                'dashboard.view',
                'employee.profile.view',
                'employee.attendance.checkin',
                'employee.attendance.checkout',
                'employee.attendance.view',
                'employee.calendar.view',
                'employee.leave.apply',
                'employee.leave.view',
                'employee.ticket.view',
                'employee.ticket.update',
                'employee.daily-report.view',
                'employee.daily-report.create',
                'holiday.view',
                'holidays.view',
            ]);

        $this->syncMenuPermissions();
        $this->syncAdminMenus();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function syncAdminMenus(): void
    {
        if (!Schema::hasTable('menus') || !Schema::hasTable('role_menu')) {
            return;
        }

        $menuIds = \App\Models\Menu::pluck('id')->all();
        if (empty($menuIds)) {
            return;
        }

        Role::whereIn('name', ['Super Admin', 'Admin'])
            ->where('guard_name', 'web')
            ->get()
            ->each(function (Role $role) use ($menuIds) {
                $payload = collect($menuIds)->mapWithKeys(fn ($menuId) => [
                    $menuId => [
                        'can_view' => 1,
                        'can_edit' => 1,
                    ],
                ])->all();

                $role->menus()->syncWithoutDetaching($payload);
            });
    }

    private function syncMenuPermissions(): void
    {
        if (!Schema::hasTable('menus')) {
            return;
        }

        $menuPermissions = [
            'client.dashboard' => ['client.dashboard.view', 'client'],
            'client.tickets.index' => ['client.tickets.manage', 'client'],
            'admin.tickets.index' => ['admin.client.tickets.manage', 'web'],
            'attendance.attendance-dashboard.org-reports' => ['team.attendance.view', 'web'],
        ];

        foreach ($menuPermissions as $route => [$permissionName, $guardName]) {
            $permission = Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => $guardName,
            ]);

            \App\Models\Menu::where('route', $route)->update([
                'permission_id' => $permission->id,
            ]);
        }

        $clientRole = Role::where('name', 'Client')->where('guard_name', 'client')->first();
        if ($clientRole) {
            $clientRole->syncPermissions(
                Permission::where('guard_name', 'client')->get()
            );
        }
    }
}
