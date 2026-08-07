<?php

namespace Database\Seeders;

use App\Enums\BloodGroup;
use App\Enums\Gender;
use App\Enums\PackageDuration;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\CareOff;
use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\FormOwner;
use App\Models\MaktabCategory;
use App\Models\MehramRelation;
use App\Models\Package;
use App\Models\Pilgrim;
use App\Models\RoomType;
use App\Models\WarisRelation;
use App\Services\PilgrimService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HajjDemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $pakistan = Country::query()->where('iso2', 'PK')->first();
        $saudi = Country::query()->where('iso2', 'SA')->first();

        if (! $pakistan || ! $saudi) {
            $this->command->warn('Countries not seeded. Run CountriesSeeder first.');

            return;
        }

        $company = Company::query()->updateOrCreate(
            ['code' => 'DYN'],
            [
                'name' => 'Deyar-e-Noor Hajj Umrah & Services',
                'legal_name' => 'Deyar-e-Noor Hajj Umrah & Services (Pvt) Ltd.',
                'enr_number' => 'ENR-2026-001',
                'munazzam_code' => 'MUN-DYN-001',
                'email' => 'info@deyarenoor.com',
                'phone' => '+92-300-1234567',
                'address' => 'Main Boulevard, Lahore',
                'city' => 'Lahore',
                'country' => 'Pakistan',
                'currency' => 'PKR',
                'timezone' => 'Asia/Karachi',
                'is_active' => true,
            ]
        );

        $cities = collect([
            ['name' => 'Karachi', 'code' => 'KHI'],
            ['name' => 'Lahore', 'code' => 'LHE'],
            ['name' => 'Islamabad', 'code' => 'ISB'],
            ['name' => 'Jeddah', 'code' => 'JED'],
            ['name' => 'Makkah', 'code' => 'MAK'],
            ['name' => 'Madinah', 'code' => 'MED'],
        ])->mapWithKeys(function (array $city) use ($pakistan, $saudi) {
            $countryId = in_array($city['code'], ['JED', 'MAK', 'MED'], true) ? $saudi->id : $pakistan->id;

            $record = City::query()->updateOrCreate(
                ['name' => $city['name'], 'country_id' => $countryId],
                ['code' => $city['code'], 'is_active' => true]
            );

            return [$city['code'] => $record];
        });

        $formOwners = collect(['Self', 'Agent A — Lahore', 'Agent B — Karachi'])
            ->map(fn (string $name) => FormOwner::query()->updateOrCreate(
                ['name' => $name],
                ['is_active' => true]
            ));

        $maktabCategories = collect([
            ['name' => 'Category A', 'zone' => 'Zone 1'],
            ['name' => 'Category B', 'zone' => 'Zone 2'],
            ['name' => 'Category C', 'zone' => 'Zone 3'],
        ])->map(fn (array $data) => MaktabCategory::query()->updateOrCreate(
            ['name' => $data['name']],
            ['zone' => $data['zone'], 'is_active' => true]
        ));

        $packages = collect([
            ['number' => 'PKG-001', 'name' => 'Economy Hajj Package', 'price' => 850000, 'days' => 21, 'qurbani' => true, 'duration' => PackageDuration::Long],
            ['number' => 'PKG-002', 'name' => 'Standard Hajj Package', 'price' => 1050000, 'days' => 25, 'qurbani' => true, 'duration' => PackageDuration::Long],
            ['number' => 'PKG-003', 'name' => 'Short Hajj Package', 'price' => 650000, 'days' => 14, 'qurbani' => false, 'duration' => PackageDuration::Short],
        ])->map(fn (array $data) => Package::query()->updateOrCreate(
            ['number' => $data['number']],
            [
                'name' => $data['name'],
                'price' => $data['price'],
                'days' => $data['days'],
                'qurbani_included' => $data['qurbani'],
                'duration' => $data['duration'],
                'is_active' => true,
            ]
        ));

        $careOffs = collect(['Deyar-e-Noor Head Office', 'Lahore Branch', 'Karachi Branch'])
            ->map(fn (string $name) => CareOff::query()->updateOrCreate(
                ['name' => $name],
                ['is_active' => true]
            ));

        $roomTypes = collect(['Sharing', 'Double', 'Triple', 'Quad'])
            ->map(fn (string $name) => RoomType::query()->updateOrCreate(
                ['name' => $name],
                ['is_active' => true]
            ));

        $mehramRelations = collect(['Husband', 'Wife', 'Brother', 'Son', 'Father', 'Uncle'])
            ->map(fn (string $name) => MehramRelation::query()->updateOrCreate(
                ['name' => $name],
                ['is_active' => true]
            ));

        $warisRelations = collect(['Son', 'Brother', 'Father', 'Nephew', 'Uncle'])
            ->map(fn (string $name) => WarisRelation::query()->updateOrCreate(
                ['name' => $name],
                ['is_active' => true]
            ));

        Airline::query()->updateOrCreate(
            ['code' => 'PK'],
            [
                'name' => 'Pakistan International Airlines',
                'iata_code' => 'PK',
                'icao_code' => 'PIA',
                'country_id' => $pakistan->id,
                'is_active' => true,
            ]
        );

        Airline::query()->updateOrCreate(
            ['code' => 'SV'],
            [
                'name' => 'Saudia',
                'iata_code' => 'SV',
                'icao_code' => 'SVA',
                'country_id' => $saudi->id,
                'is_active' => true,
            ]
        );

        Airport::query()->updateOrCreate(
            ['code' => 'KHI'],
            ['name' => 'Jinnah International Airport', 'city_id' => $cities['KHI']->id, 'is_active' => true]
        );

        Airport::query()->updateOrCreate(
            ['code' => 'LHE'],
            ['name' => 'Allama Iqbal International Airport', 'city_id' => $cities['LHE']->id, 'is_active' => true]
        );

        Airport::query()->updateOrCreate(
            ['code' => 'JED'],
            ['name' => 'King Abdulaziz International Airport', 'city_id' => $cities['JED']->id, 'is_active' => true]
        );

        if (Pilgrim::query()->exists()) {
            $this->command->info('Hajj demo data seeded (pilgrims already exist, skipped demo pilgrims).');

            return;
        }

        $pilgrimService = app(PilgrimService::class);
        $hajjYear = (int) now()->year;

        $demoPilgrims = [
            [
                'suffix' => 'A',
                'gender' => Gender::Male,
                'surname' => 'Khan',
                'given_name' => 'Ahmed',
                'father_husband_name' => 'Muhammad Khan',
                'passport_no' => 'AB1234567',
                'date_of_birth' => '1975-03-15',
                'birth_place' => 'Lahore',
                'cnic' => '35201-1234567-1',
                'mobile' => '0300-1234567',
                'mehram_name' => 'Fatima Khan',
                'mehram_relation' => 'Wife',
                'waris_name' => 'Ali Khan',
                'waris_cnic' => '35201-7654321-3',
                'waris_relation' => 'Son',
                'waris_mobile' => '0300-9876543',
            ],
            [
                'suffix' => 'B',
                'gender' => Gender::Female,
                'surname' => 'Khan',
                'given_name' => 'Fatima',
                'father_husband_name' => 'Ahmed Khan',
                'passport_no' => 'CD7654321',
                'date_of_birth' => '1980-07-22',
                'birth_place' => 'Lahore',
                'cnic' => '35201-2345678-2',
                'mobile' => '0300-2345678',
                'mehram_name' => 'Ahmed Khan',
                'mehram_relation' => 'Husband',
                'waris_name' => 'Ali Khan',
                'waris_cnic' => '35201-7654321-3',
                'waris_relation' => 'Son',
                'waris_mobile' => '0300-9876543',
            ],
            [
                'suffix' => 'S',
                'gender' => Gender::Male,
                'surname' => 'Malik',
                'given_name' => 'Usman',
                'father_husband_name' => 'Tariq Malik',
                'passport_no' => 'EF9876543',
                'date_of_birth' => '1968-11-05',
                'birth_place' => 'Karachi',
                'cnic' => '42101-3456789-1',
                'mobile' => '0321-4567890',
                'mehram_name' => 'Saima Malik',
                'mehram_relation' => 'Wife',
                'waris_name' => 'Tariq Malik',
                'waris_cnic' => '42101-8765432-5',
                'waris_relation' => 'Brother',
                'waris_mobile' => '0321-1112233',
            ],
        ];

        $familyNumber = 1;

        foreach ($demoPilgrims as $index => $demo) {
            $isFamily = $index < 2;
            $suffix = $demo['suffix'];
            $family = $isFamily
                ? [
                    'family_code' => $pilgrimService->formatFamilyCode($company, $familyNumber, $suffix),
                    'family_number' => $familyNumber,
                    'family_member_suffix' => $suffix,
                ]
                : $pilgrimService->prepareNewSingleFamily($company);

            if ($index === 2) {
                $familyNumber++;
            }

            $mehramRelation = $mehramRelations->firstWhere('name', $demo['mehram_relation'])
                ?? $mehramRelations->first();
            $warisRelation = $warisRelations->firstWhere('name', $demo['waris_relation'])
                ?? $warisRelations->first();

            $dob = Carbon::parse($demo['date_of_birth']);

            Pilgrim::query()->create([
                'hajj_year' => $hajjYear,
                'booking_date' => now()->toDateString(),
                'form_owner_id' => $formOwners->first()->id,
                'company_id' => $company->id,
                'maktab_category_id' => $maktabCategories->first()->id,
                'package_id' => $packages->first()->id,
                'care_off_id' => $careOffs->first()->id,
                'pod_city_id' => $cities['LHE']->id,
                'room_type_id' => $roomTypes->first()->id,
                'gender' => $demo['gender'],
                'surname' => $demo['surname'],
                'given_name' => $demo['given_name'],
                'father_husband_name' => $demo['father_husband_name'],
                'full_name' => $pilgrimService->buildFullName($demo['surname'], $demo['given_name']),
                'passport_no' => $demo['passport_no'],
                'date_of_birth' => $dob,
                'birth_place' => $demo['birth_place'],
                'passport_expiry' => now()->addYears(3)->toDateString(),
                'address' => 'House 12, Model Town, '.$demo['birth_place'],
                'mobile' => $demo['mobile'],
                'cnic' => $demo['cnic'],
                'blood_group' => BloodGroup::OPositive,
                'mehram_name' => $demo['mehram_name'],
                'mehram_relation_id' => $mehramRelation->id,
                'waris_name' => $demo['waris_name'],
                'waris_cnic' => $demo['waris_cnic'],
                'waris_relation_id' => $warisRelation->id,
                'waris_mobile' => $demo['waris_mobile'],
                'family_code' => $family['family_code'],
                'family_number' => $family['family_number'],
                'family_member_suffix' => $family['family_member_suffix'],
                'age' => $pilgrimService->calculateAge($dob, $hajjYear),
            ]);
        }

        $this->command->info('Hajj demo data seeded successfully.');
    }
}
