<?php

use App\Models\Company;
use App\Models\Pilgrim;
use Database\Seeders\CountriesSeeder;
use Database\Seeders\HajjDemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('hajj demo data seeder creates sample registrations', function () {
    Storage::fake('public');

    $this->seed(CountriesSeeder::class);
    $this->seed(HajjDemoDataSeeder::class);

    expect(Company::query()->where('code', 'DYN')->exists())->toBeTrue()
        ->and(Company::query()->where('code', 'AHT')->exists())->toBeTrue()
        ->and(Pilgrim::query()->where('passport_no', 'AB1234567')->value('family_code'))->toBe('DYN-01-A')
        ->and(Pilgrim::query()->where('passport_no', 'CD9876543')->value('family_code'))->toBe('AHT-01-S')
        ->and(Pilgrim::query()->where('passport_no', 'DRAFT0001')->exists())->toBeTrue()
        ->and(Pilgrim::query()->whereNotNull('photo_path')->count())->toBeGreaterThanOrEqual(4);
});
