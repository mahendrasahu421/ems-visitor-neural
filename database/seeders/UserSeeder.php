<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminPassword = (string) env('SEED_SUPER_ADMIN_PASSWORD');
        $adminPassword = (string) env('SEED_ADMIN_PASSWORD');

        if ($superAdminPassword === '' || $adminPassword === '') {
            throw new \RuntimeException(
                'Set SEED_SUPER_ADMIN_PASSWORD and SEED_ADMIN_PASSWORD before seeding user accounts.'
            );
        }

        $this->upsertUserWithRole([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make($superAdminPassword),
            'status' => 1,
        ], 'Super Admin');

        $this->upsertUserWithRole([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make($adminPassword),
            'status' => 1,
        ], 'Admin');

        $this->upsertUserWithRole([
            'name' => 'HR User',
            'email' => 'hr@example.com',
            'password' => Hash::make('admin123'),
            'status' => 1,
        ], 'HR');

        $this->upsertUserWithRole([
            'name' => 'Employee User',
            'email' => 'employee@example.com',
            'password' => Hash::make('admin123'),
            'status' => 1,
        ], 'Employee');
    }

    private function upsertUserWithRole(array $attributes, string $roleName): void
    {
        $role = Role::firstOrCreate([
            'name' => $roleName,
            'guard_name' => 'web',
        ]);

        $user = User::updateOrCreate(
            ['email' => $attributes['email']],
            [
                'name' => $attributes['name'],
                'password' => $attributes['password'],
                'role_id' => $role->id,
                'status' => $attributes['status'],
            ]
        );

        $user->syncRoles([$role->name]);
    }
}
