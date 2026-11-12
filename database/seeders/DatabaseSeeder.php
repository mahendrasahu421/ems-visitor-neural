<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call([
            MaritalStatusSeeder::class,
        ]);
        $this->call([
            GenderSeeder::class,
        ]);
        $this->call(RolePermissionSeeder::class);
        $this->call(SupervisorRoleAndPermissionsSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ClientPortalSeeder::class);
        // $this->call(ClientSeeder::class);
        $this->call([
            CountrySeeder::class,
        ]);
        $this->call([
            StateSeeder::class,
        ]);
        $this->call([
            CitySeeder::class,
        ]);
         $this->call(JobCategorySeeder::class);
         $this->call(SkillSeeder::class);
         $this->call(LeaveTypeSeeder::class);
    }


}
