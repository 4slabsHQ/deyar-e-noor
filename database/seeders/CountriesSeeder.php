<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesSeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'Pakistan',              'iso2' => 'PK', 'iso3' => 'PAK', 'phone_code' => '+92',  'flag' => '🇵🇰'],
            ['name' => 'United Arab Emirates',  'iso2' => 'AE', 'iso3' => 'ARE', 'phone_code' => '+971', 'flag' => '🇦🇪'],
            ['name' => 'Saudi Arabia',          'iso2' => 'SA', 'iso3' => 'SAU', 'phone_code' => '+966', 'flag' => '🇸🇦'],
            ['name' => 'United Kingdom',        'iso2' => 'GB', 'iso3' => 'GBR', 'phone_code' => '+44',  'flag' => '🇬🇧'],
            ['name' => 'United States',         'iso2' => 'US', 'iso3' => 'USA', 'phone_code' => '+1',   'flag' => '🇺🇸'],
            ['name' => 'Turkey',                'iso2' => 'TR', 'iso3' => 'TUR', 'phone_code' => '+90',  'flag' => '🇹🇷'],
            ['name' => 'Malaysia',              'iso2' => 'MY', 'iso3' => 'MYS', 'phone_code' => '+60',  'flag' => '🇲🇾'],
            ['name' => 'Qatar',                 'iso2' => 'QA', 'iso3' => 'QAT', 'phone_code' => '+974', 'flag' => '🇶🇦'],
            ['name' => 'Kuwait',                'iso2' => 'KW', 'iso3' => 'KWT', 'phone_code' => '+965', 'flag' => '🇰🇼'],
            ['name' => 'Bahrain',               'iso2' => 'BH', 'iso3' => 'BHR', 'phone_code' => '+973', 'flag' => '🇧🇭'],
            ['name' => 'Oman',                  'iso2' => 'OM', 'iso3' => 'OMN', 'phone_code' => '+968', 'flag' => '🇴🇲'],
            ['name' => 'Jordan',                'iso2' => 'JO', 'iso3' => 'JOR', 'phone_code' => '+962', 'flag' => '🇯🇴'],
            ['name' => 'Egypt',                 'iso2' => 'EG', 'iso3' => 'EGY', 'phone_code' => '+20',  'flag' => '🇪🇬'],
            ['name' => 'Iran',                  'iso2' => 'IR', 'iso3' => 'IRN', 'phone_code' => '+98',  'flag' => '🇮🇷'],
            ['name' => 'India',                 'iso2' => 'IN', 'iso3' => 'IND', 'phone_code' => '+91',  'flag' => '🇮🇳'],
            ['name' => 'Bangladesh',            'iso2' => 'BD', 'iso3' => 'BGD', 'phone_code' => '+880', 'flag' => '🇧🇩'],
            ['name' => 'Afghanistan',           'iso2' => 'AF', 'iso3' => 'AFG', 'phone_code' => '+93',  'flag' => '🇦🇫'],
            ['name' => 'Indonesia',             'iso2' => 'ID', 'iso3' => 'IDN', 'phone_code' => '+62',  'flag' => '🇮🇩'],
        ];

        foreach ($countries as $country) {
            DB::table('countries')->updateOrInsert(
                ['iso2' => $country['iso2']],
                array_merge($country, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        $this->command->info('Countries seeded successfully.');
    }
}