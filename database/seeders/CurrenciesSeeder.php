<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrenciesSeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['name' => 'Pakistani Rupee',        'code' => 'PKR', 'symbol' => '₨',  'exchange_rate' => 1.000000,  'is_default' => true,  'is_active' => true],
            ['name' => 'UAE Dirham',             'code' => 'AED', 'symbol' => 'د.إ', 'exchange_rate' => 0.013000, 'is_default' => false, 'is_active' => true],
            ['name' => 'Saudi Riyal',            'code' => 'SAR', 'symbol' => '﷼',  'exchange_rate' => 0.013500, 'is_default' => false, 'is_active' => true],
            ['name' => 'US Dollar',              'code' => 'USD', 'symbol' => '$',   'exchange_rate' => 0.003600, 'is_default' => false, 'is_active' => true],
            ['name' => 'British Pound',          'code' => 'GBP', 'symbol' => '£',   'exchange_rate' => 0.002800, 'is_default' => false, 'is_active' => true],
            ['name' => 'Euro',                   'code' => 'EUR', 'symbol' => '€',   'exchange_rate' => 0.003100, 'is_default' => false, 'is_active' => true],
            ['name' => 'Qatari Riyal',           'code' => 'QAR', 'symbol' => 'ر.ق', 'exchange_rate' => 0.013100, 'is_default' => false, 'is_active' => true],
            ['name' => 'Kuwaiti Dinar',          'code' => 'KWD', 'symbol' => 'د.ك', 'exchange_rate' => 0.001100, 'is_default' => false, 'is_active' => true],
            ['name' => 'Turkish Lira',           'code' => 'TRY', 'symbol' => '₺',  'exchange_rate' => 0.110000, 'is_default' => false, 'is_active' => true],
            ['name' => 'Malaysian Ringgit',      'code' => 'MYR', 'symbol' => 'RM',  'exchange_rate' => 0.016000, 'is_default' => false, 'is_active' => true],
        ];

        foreach ($currencies as $currency) {
            DB::table('currencies')->updateOrInsert(
                ['code' => $currency['code']],
                array_merge($currency, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        $this->command->info('Currencies seeded successfully.');
    }
}