<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SupervisorRoleAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Supervisor Role if not exists
        $supervisorRole = Role::firstOrCreate(['name' => 'Supervisor', 'guard_name' => 'web']);

        // 2. Define Supervisor Permissions
        $supervisorPermissions = [
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
        ];

        foreach ($supervisorPermissions as $perm) {
            $permission = Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
            $supervisorRole->givePermissionTo($permission);
        }

        // 3. Define Employee Permissions
        $employeeRole = Role::firstOrCreate(['name' => 'Employee', 'guard_name' => 'web']);
        $employeePermissions = [
            'employee.profile.view',
            'employee.attendance.checkin',
            'employee.attendance.checkout',
            'employee.attendance.view',
            'employee.calendar.view',
            'employee.leave.apply',
            'employee.leave.view',
            'holidays.view',
        ];

        foreach ($employeePermissions as $perm) {
            $permission = Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
            $employeeRole->givePermissionTo($permission);
        }
    }
}
