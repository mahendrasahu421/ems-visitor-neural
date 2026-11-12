<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('leave_types')->insert([
            [
                'company_id' => 1,
                'leave_name' => 'Casual Leave',
                'leave_code' => 'CL-001',
                'total_leaves' => 12,
                'carry_forward' => 1,
                'encashable' => 0,
                'applicable_for' => 'All',
                'leave_category' => 'Paid',
                'status' => 'Active',
                'description' => 'Casual leave for personal work or emergencies.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'leave_name' => 'Sick Leave',
                'leave_code' => 'SL-001',
                'total_leaves' => 10,
                'carry_forward' => 1,
                'encashable' => 0,
                'applicable_for' => 'All',
                'leave_category' => 'Paid',
                'status' => 'Active',
                'description' => 'Sick leave for medical or health-related issues.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'leave_name' => 'Earned Leave',
                'leave_code' => 'EL-001',
                'total_leaves' => 15,
                'carry_forward' => 1,
                'encashable' => 1,
                'applicable_for' => 'All',
                'leave_category' => 'Paid',
                'status' => 'Active',
                'description' => 'Earned leave accumulated over time.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'leave_name' => 'Maternity Leave',
                'leave_code' => 'ML-001',
                'total_leaves' => 180,
                'carry_forward' => 0,
                'encashable' => 0,
                'applicable_for' => 'Female',
                'leave_category' => 'Special',
                'status' => 'Active',
                'description' => 'Leave for female employees during maternity period.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
