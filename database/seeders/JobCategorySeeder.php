<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobCategory;

class JobCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Information Technology', 'type' => 'Industry', 'status' => 'Active'],
            ['name' => 'Human Resources', 'type' => 'Industry', 'status' => 'Active'],
            ['name' => 'Finance & Accounts', 'type' => 'Industry', 'status' => 'Active'],
            ['name' => 'Sales & Marketing', 'type' => 'Industry', 'status' => 'Active'],
            ['name' => 'Operations', 'type' => 'Industry', 'status' => 'Active'],
            ['name' => 'Full-Time', 'type' => 'Job Type', 'status' => 'Active'],
            ['name' => 'Part-Time', 'type' => 'Job Type', 'status' => 'Active'],
            ['name' => 'Contract', 'type' => 'Job Type', 'status' => 'Active'],
            ['name' => 'Internship', 'type' => 'Job Type', 'status' => 'Active'],
        ];

        foreach ($categories as $category) {
            JobCategory::create($category);
        }
    }
}
