<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Holiday;

class Holiday2026Seeder extends Seeder
{
    public function run()
    {
        // 5 Common Holidays (Mandatory - All offices closed)
        $commonHolidays = [
            [
                'holiday_name' => 'Republic Day',
                'holiday_date' => '2026-01-26',
                'holiday_remark' => 'Common Holiday - Mandatory Office Closed',
                'status' => 1,
            ],
            [
                'holiday_name' => 'Holi',
                'holiday_date' => '2026-03-04',
                'holiday_remark' => 'Common Holiday - Mandatory Office Closed',
                'status' => 1,
            ],
            [
                'holiday_name' => 'Mahatma Gandhi Birthday',
                'holiday_date' => '2026-10-02',
                'holiday_remark' => 'Common Holiday - Mandatory Office Closed',
                'status' => 1,
            ],
            [
                'holiday_name' => 'Durga Puja (Maha Navami)/Dussehra',
                'holiday_date' => '2026-10-20',
                'holiday_remark' => 'Common Holiday - Mandatory Office Closed',
                'status' => 1,
            ],
            [
                'holiday_name' => 'Christmas Day',
                'holiday_date' => '2026-12-25',
                'holiday_remark' => 'Common Holiday - Mandatory Office Closed',
                'status' => 1,
            ],
        ];

        // 22 Restricted Holidays (Optional - Employees can opt for any 5)
        $restrictedHolidays = [
            [
                'holiday_name' => 'Lohri',
                'holiday_date' => '2026-01-13',
                'holiday_remark' => 'Restricted Holiday - Optional (Any 5)',
                'status' => 1,
            ],
            [
                'holiday_name' => 'Makar Sankranti/Magh Bihu/ Pongal',
                'holiday_date' => '2026-01-14',
                'holiday_remark' => 'Restricted Holiday - Optional (Any 5)',
                'status' => 1,
            ],
            [
                'holiday_name' => 'Basant Panchami',
                'holiday_date' => '2026-01-23',
                'holiday_remark' => 'Restricted Holiday - Optional (Any 5)',
                'status' => 1,
            ],
            [
                'holiday_name' => 'Holika Dahan/ Doljatra',
                'holiday_date' => '2026-03-03',
                'holiday_remark' => 'Restricted Holiday - Optional (Any 5)',
                'status' => 1,
            ],
            [
                'holiday_name' => 'Chaitra Sukladi / Gudi Padava / Ugadi / Cheti Chand',
                'holiday_date' => '2026-03-19',
                'holiday_remark' => 'Restricted Holiday - Optional (Any 5)',
                'status' => 1,
            ],
            [
                'holiday_name' => 'Ram Navami',
                'holiday_date' => '2026-03-26',
                'holiday_remark' => 'Restricted Holiday - Optional (Any 5)',
                'status' => 1,
            ],
            [
                'holiday_name' => 'Mahavir Jayanti',
                'holiday_date' => '2026-03-31',
                'holiday_remark' => 'Restricted Holiday - Optional (Any 5)',
                'status' => 1,
            ],
            [
                'holiday_name' => 'Odisha Day (Utkal Divas)',
                'holiday_date' => '2026-04-01',
                'holiday_remark' => 'Restricted Holiday - Optional (Any 5)',
                'status' => 1,
            ],
            [
                'holiday_name' => 'Good Friday',
                'holiday_date' => '2026-04-03',
                'holiday_remark' => 'Restricted Holiday - Optional (Any 5)',
                'status' => 1,
            ],
            [
                'holiday_name' => 'Vaisakhi/Vishu/Meshadi/Tamil New Year',
                'holiday_date' => '2026-04-14',
                'holiday_remark' => 'Restricted Holiday - Optional (Any 5)',
                'status' => 1,
            ],
            [
                'holiday_name' => 'Vaishakhi (Bengal)/Bahag Bihu (Assam)',
                'holiday_date' => '2026-04-15',
                'holiday_remark' => 'Restricted Holiday - Optional (Any 5)',
                'status' => 1,
            ],
            [
                'holiday_name' => 'Maharashtra Day/ May Day/Buddha Purnima',
                'holiday_date' => '2026-05-01',
                'holiday_remark' => 'Restricted Holiday - Optional (Any 5)',
                'status' => 1,
            ],
            [
                'holiday_name' => 'Id-Ul-Zuha (Bakrid)',
                'holiday_date' => '2026-05-28',
                'holiday_remark' => 'Restricted Holiday - Optional (Any 5)',
                'status' => 1,
            ],
            [
                'holiday_name' => 'Muharram',
                'holiday_date' => '2026-06-26',
                'holiday_remark' => 'Restricted Holiday - Optional (Any 5)',
                'status' => 1,
            ],
            [
                'holiday_name' => 'Rath Yatra',
                'holiday_date' => '2026-07-16',
                'holiday_remark' => 'Restricted Holiday - Optional (Any 5)',
                'status' => 1,
            ],
            [
                'holiday_name' => 'Onam or Thiru Onam Day',
                'holiday_date' => '2026-08-26',
                'holiday_remark' => 'Restricted Holiday - Optional (Any 5)',
                'status' => 1,
            ],
            [
                'holiday_name' => 'Raksha Bandhan',
                'holiday_date' => '2026-08-28',
                'holiday_remark' => 'Restricted Holiday - Optional (Any 5)',
                'status' => 1,
            ],
            [
                'holiday_name' => 'Janmashtami',
                'holiday_date' => '2026-09-04',
                'holiday_remark' => 'Restricted Holiday - Optional (Any 5)',
                'status' => 1,
            ],
            [
                'holiday_name' => 'Ganesh Chaturthi/ Vinayaka Chaturthi',
                'holiday_date' => '2026-09-14',
                'holiday_remark' => 'Restricted Holiday - Optional (Any 5)',
                'status' => 1,
            ],
            [
                'holiday_name' => 'Vishwakarma Puja',
                'holiday_date' => '2026-09-17',
                'holiday_remark' => 'Restricted Holiday - Optional (Any 5)',
                'status' => 1,
            ],
            [
                'holiday_name' => 'Durga Puja (Maha Ashtami)',
                'holiday_date' => '2026-10-19',
                'holiday_remark' => 'Restricted Holiday - Optional (Any 5)',
                'status' => 1,
            ],
            [
                'holiday_name' => 'Karwa Chauth',
                'holiday_date' => '2026-10-29',
                'holiday_remark' => 'Restricted Holiday - Optional (Any 5)',
                'status' => 1,
            ],
        ];

        // Remove old inactive 2026 holidays if any exist
        Holiday::whereYear('holiday_date', 2026)->delete();

        $all = array_merge($commonHolidays, $restrictedHolidays);
        foreach ($all as $item) {
            Holiday::create($item);
        }
    }
}
