<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InvoiceClientMasterSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('clientmaster')) {
            $this->command?->error('clientmaster table does not exist.');

            return;
        }

        $clientNames = [
            'MAMTA Health Institute for Mother and Child',
            'Mahmood Saeed Beverage Cans || Ends Industry',
            'Har Narayan Singh Har Narayan Singh Degree Colleg',
            'Mahmood Saeed',
            'Swami Paramhansa Sansthan',
            'InfoPark Technologies',
            'Shree Maa Multichem',
            'ThreevoLab',
            'SIS India',
            'Neural Info Solutions Pvt Ltd',
            'Eventstan',
            'CRY',
            'IASSW AEITS',
            'Unionyogaayurveda',
            'Ideal Pco Licence',
            'IISSM',
            'others',
            'SHREE NATH FAB CARE PVT LTD',
            'NAS Legal',
            'Vandana Medicare Multispecialty Ayurveda Hospital',
            'Caritas India',
            'Wordpress website',
            'Vyasa Delhi',
            'ETechno Mind',
            'KNOWLEDGEFLEX TECHNOLOGIES PVT LTD',
            'Vira Properties',
            'Durgawati School',
            'Eventstan FZE',
            'Softek',
            'GO GO Pharma',
            'Mamta eLearning',
            'Daksh properties',
            'Dr S K Gulati Plastic || Cosmetic Surgeon',
            'Aroham',
            'Lotus Yoga',
            'Solitaire Groups',
            'PHI',
            'Roboclubindia',
            'Neural',
            'Holistic Wellness',
            'Shigan EV Ltd',
            'AP-SHWP',
            'ABCDEF',
            'ACFL, Patna',
            'Mgracesolution.com',
            'Sheetanshu Shrivastava',
            'NA',
            'Madhav Pipes',
            'KK Sinha',
            'Suraj Mahajan',
            'The scared chants',
            'Mr. Rashik',
            'Manish Srivastava',
            'Sunaina Mam',
            'Lakshya the targets',
            'Nishant',
            'ABC',
            'ACFL PMP',
            'Manas',
            'Suraj Mahajan , SIS',
            'Nayan Shukla',
            'One SIS Solutions Pvt Lmt',
            'Sachin Shinha',
        ];

        $inactiveClientNames = [
            'Mahmood Saeed Beverage Cans || Ends Industry',
            'Har Narayan Singh Har Narayan Singh Degree Colleg',
            'Mahmood Saeed',
            'ThreevoLab',
            'Ideal Pco Licence',
            'IISSM',
            'others',
            'SHREE NATH FAB CARE PVT LTD',
            'Vandana Medicare Multispecialty Ayurveda Hospital',
            'Caritas India',
            'Vyasa Delhi',
            'KNOWLEDGEFLEX TECHNOLOGIES PVT LTD',
            'GO GO Pharma',
            'Daksh properties',
            'Aroham',
            'Lotus Yoga',
            'Roboclubindia',
            'Neural',
            'Holistic Wellness',
            'Shigan EV Ltd',
            'ABCDEF',
        ];

        $indiaId = (string) (DB::table('countrymaster')
            ->whereRaw("LOWER(TRIM(CountryName)) = 'india'")
            ->value('CountryID') ?? '0');

        $stateId = $indiaId !== '0'
            ? (string) (DB::table('statemaster')->where('CountryID', $indiaId)->orderBy('StateID')->value('StateID') ?? '')
            : '';

        $inserted = 0;
        $updated = 0;

        foreach ($clientNames as $clientName) {
            $exists = DB::table('clientmaster')->where('ClientName', $clientName)->exists();

            DB::table('clientmaster')->updateOrInsert(
                ['ClientName' => $clientName],
                [
                    'CompanyID' => 11,
                    'ProductCategoryType' => null,
                    'CategoryType' => null,
                    'ClientCode' => '',
                    'Priority' => '',
                    'Source' => null,
                    'ConcernOwner' => 'NA',
                    'Address' => 'NA',
                    'MobileNo' => '0000000000',
                    'Fax' => '',
                    'website' => '',
                    'client_logo' => '',
                    'gst_number' => '',
                    'pinNo' => '',
                    'EmailAddress' => '',
                    'Country' => $indiaId,
                    'StateID' => $stateId,
                    'PhoneNo' => '',
                    'DelStatus' => in_array($clientName, $inactiveClientNames, true) ? 0 : 1,
                    'created_at' => now(),
                ]
            );

            $exists ? $updated++ : $inserted++;
        }

        $this->command?->info("clientmaster seeded. Inserted: {$inserted}. Updated: {$updated}. Total names: " . count($clientNames) . '.');
    }
}
