<?php

namespace Database\Seeders;

use App\Enums\BloodGroup;
use App\Enums\Gender;
use App\Enums\PackageDuration;
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
use Illuminate\Support\Facades\Storage;

class HajjDemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $hajjYear = (int) now()->year;
        $service = app(PilgrimService::class);

        $pakistan = Country::query()->where('iso2', 'PK')->first();

        if ($pakistan === null) {
            $this->command?->warn('Pakistan not found. Run CountriesSeeder first.');

            return;
        }

        $lahore = City::query()->updateOrCreate(
            ['name' => 'Lahore', 'country_id' => $pakistan->id],
            ['is_active' => true],
        );

        $karachi = City::query()->updateOrCreate(
            ['name' => 'Karachi', 'country_id' => $pakistan->id],
            ['is_active' => true],
        );

        $formOwnerSelf = FormOwner::query()->updateOrCreate(
            ['name' => 'Self'],
            ['is_active' => true],
        );

        $formOwnerAgent = FormOwner::query()->updateOrCreate(
            ['name' => 'Agent'],
            ['is_active' => true],
        );

        $maktabCategory = MaktabCategory::query()->updateOrCreate(
            ['name' => 'Category A'],
            ['zone' => 'Zone 1', 'is_active' => true],
        );

        $economyPackage = Package::query()->updateOrCreate(
            ['number' => 'PKG-001'],
            [
                'name' => 'Economy',
                'price' => 850000,
                'days' => 21,
                'qurbani_included' => true,
                'duration' => PackageDuration::Long,
                'is_active' => true,
            ],
        );

        $premiumPackage = Package::query()->updateOrCreate(
            ['number' => 'PKG-002'],
            [
                'name' => 'Premium',
                'price' => 1250000,
                'days' => 28,
                'qurbani_included' => true,
                'duration' => PackageDuration::Long,
                'is_active' => true,
            ],
        );

        $careOff = CareOff::query()->updateOrCreate(
            ['name' => 'Head Office'],
            ['is_active' => true],
        );

        $roomType = RoomType::query()->updateOrCreate(
            ['name' => 'Sharing'],
            ['is_active' => true],
        );

        $mehramRelation = MehramRelation::query()->updateOrCreate(
            ['name' => 'Husband'],
            ['is_active' => true],
        );

        $warisRelation = WarisRelation::query()->updateOrCreate(
            ['name' => 'Son'],
            ['is_active' => true],
        );

        $deyarLogo = $this->seedStorageImage('companies/logos/deyar-e-noor.png');
        $haramLogo = $this->seedStorageImage('companies/logos/al-haram-travels.png');

        $deyar = Company::query()->updateOrCreate(
            ['code' => 'DYN'],
            [
                'name' => 'Deyar-e-Noor',
                'legal_name' => 'Deyar-e-Noor Hajj Umrah & Services (Pvt) Ltd.',
                'enr_number' => 'ENR-DYN-001',
                'munazzam_code' => 'MZ-DYN-100',
                'quota' => 500,
                'logo' => $deyarLogo,
                'city' => 'Lahore',
                'country' => 'Pakistan',
                'is_active' => true,
            ],
        );

        $alHaram = Company::query()->updateOrCreate(
            ['code' => 'AHT'],
            [
                'name' => 'Al-Haram Travels',
                'legal_name' => 'Al-Haram Travels (Pvt) Ltd.',
                'enr_number' => 'ENR-AHT-001',
                'munazzam_code' => 'MZ-AHT-200',
                'quota' => 100,
                'logo' => $haramLogo,
                'city' => 'Karachi',
                'country' => 'Pakistan',
                'is_active' => true,
            ],
        );

        $noorHajj = Company::query()->updateOrCreate(
            ['code' => 'NHS'],
            [
                'name' => 'Noor Hajj Services',
                'legal_name' => 'Noor Hajj Services',
                'enr_number' => 'ENR-NHS-001',
                'munazzam_code' => 'MZ-NHS-300',
                'quota' => null,
                'logo' => null,
                'city' => 'Islamabad',
                'country' => 'Pakistan',
                'is_active' => true,
            ],
        );

        $sharedMasters = [
            'hajj_year' => $hajjYear,
            'booking_date' => now()->toDateString(),
            'form_owner_id' => $formOwnerSelf->id,
            'maktab_category_id' => $maktabCategory->id,
            'care_off_id' => $careOff->id,
            'room_type_id' => $roomType->id,
            'mehram_relation_id' => $mehramRelation->id,
            'waris_relation_id' => $warisRelation->id,
        ];

        $this->seedPilgrim(array_merge($sharedMasters, [
            'company_id' => $deyar->id,
            'package_id' => $economyPackage->id,
            'pod_city_id' => $lahore->id,
            'gender' => Gender::Male,
            'surname' => 'Khan',
            'given_name' => 'Ahmed',
            'father_husband_name' => 'Muhammad Khan',
            'passport_no' => 'AB1234567',
            'date_of_birth' => '1975-03-15',
            'birth_place' => 'Lahore',
            'passport_expiry' => now()->addYears(2)->toDateString(),
            'address' => 'House 12, Model Town, Lahore',
            'mobile' => '0300-1234567',
            'cnic' => '35201-1234567-1',
            'blood_group' => BloodGroup::OPositive,
            'mehram_name' => 'Fatima Khan',
            'waris_name' => 'Ali Khan',
            'waris_cnic' => '35201-7654321-3',
            'waris_mobile' => '0300-9876543',
            'family_number' => 1,
            'family_member_suffix' => 'A',
            'photo_path' => $this->seedStorageImage('pilgrims/ahmed-khan.jpg'),
        ]), $service, $deyar);

        $this->seedPilgrim(array_merge($sharedMasters, [
            'company_id' => $deyar->id,
            'package_id' => $economyPackage->id,
            'pod_city_id' => $lahore->id,
            'gender' => Gender::Female,
            'surname' => 'Khan',
            'given_name' => 'Fatima',
            'father_husband_name' => 'Muhammad Khan',
            'passport_no' => 'AB1234568',
            'date_of_birth' => '1978-06-20',
            'birth_place' => 'Lahore',
            'passport_expiry' => now()->addYears(2)->toDateString(),
            'address' => 'House 12, Model Town, Lahore',
            'mobile' => '0300-1234568',
            'cnic' => '35201-2345678-2',
            'blood_group' => BloodGroup::APositive,
            'mehram_name' => 'Ahmed Khan',
            'waris_name' => 'Ali Khan',
            'waris_cnic' => '35201-7654321-3',
            'waris_mobile' => '0300-9876543',
            'family_number' => 1,
            'family_member_suffix' => 'B',
            'photo_path' => $this->seedStorageImage('pilgrims/fatima-khan.jpg'),
        ]), $service, $deyar);

        $this->seedPilgrim(array_merge($sharedMasters, [
            'company_id' => $deyar->id,
            'package_id' => $economyPackage->id,
            'pod_city_id' => $lahore->id,
            'gender' => Gender::Male,
            'surname' => 'Khan',
            'given_name' => 'Ali',
            'father_husband_name' => 'Ahmed Khan',
            'passport_no' => 'AB1234569',
            'date_of_birth' => '2000-01-10',
            'birth_place' => 'Lahore',
            'passport_expiry' => now()->addYears(3)->toDateString(),
            'address' => 'House 12, Model Town, Lahore',
            'mobile' => '0300-1234569',
            'cnic' => '35201-3456789-3',
            'blood_group' => BloodGroup::BPositive,
            'mehram_name' => 'Fatima Khan',
            'waris_name' => 'Ahmed Khan',
            'waris_cnic' => '35201-1234567-1',
            'waris_mobile' => '0300-1234567',
            'family_number' => 1,
            'family_member_suffix' => 'C',
        ]), $service, $deyar);

        $this->seedPilgrim(array_merge($sharedMasters, [
            'company_id' => $alHaram->id,
            'package_id' => $premiumPackage->id,
            'pod_city_id' => $karachi->id,
            'form_owner_id' => $formOwnerAgent->id,
            'gender' => Gender::Male,
            'surname' => 'Raza',
            'given_name' => 'Hassan',
            'father_husband_name' => 'Imran Raza',
            'passport_no' => 'CD9876543',
            'date_of_birth' => '1982-11-05',
            'birth_place' => 'Karachi',
            'passport_expiry' => now()->addYears(2)->toDateString(),
            'address' => 'Block 5, Clifton, Karachi',
            'mobile' => '0321-5556677',
            'cnic' => '42101-9876543-1',
            'blood_group' => BloodGroup::OPositive,
            'mehram_name' => 'Sana Raza',
            'waris_name' => 'Imran Raza',
            'waris_cnic' => '42101-1122334-5',
            'waris_mobile' => '0321-4445566',
            'family_number' => 1,
            'family_member_suffix' => 'S',
            'photo_path' => $this->seedStorageImage('pilgrims/hassan-raza.jpg'),
        ]), $service, $alHaram);

        $this->seedPilgrim(array_merge($sharedMasters, [
            'company_id' => $noorHajj->id,
            'package_id' => $premiumPackage->id,
            'pod_city_id' => $lahore->id,
            'gender' => Gender::Female,
            'surname' => 'Ahmed',
            'given_name' => 'Sara',
            'father_husband_name' => 'Tariq Ahmed',
            'passport_no' => 'EF5555555',
            'date_of_birth' => '1990-04-18',
            'birth_place' => 'Islamabad',
            'passport_expiry' => now()->addYears(2)->toDateString(),
            'address' => 'F-8 Markaz, Islamabad',
            'mobile' => '0333-7778899',
            'cnic' => '61101-5555555-1',
            'blood_group' => BloodGroup::ABPositive,
            'mehram_name' => 'Tariq Ahmed',
            'waris_name' => 'Tariq Ahmed',
            'waris_cnic' => '61101-6666666-2',
            'waris_mobile' => '0333-8889900',
            'family_number' => 1,
            'family_member_suffix' => 'S',
            'photo_path' => $this->seedStorageImage('pilgrims/sara-ahmed.jpg'),
        ]), $service, $noorHajj);

        Pilgrim::query()->updateOrCreate(
            [
                'hajj_year' => $hajjYear,
                'passport_no' => 'DRAFT0001',
            ],
            [
                'booking_date' => null,
                'form_owner_id' => null,
                'company_id' => null,
                'maktab_category_id' => null,
                'package_id' => null,
                'care_off_id' => null,
                'pod_city_id' => null,
                'room_type_id' => null,
                'gender' => null,
                'surname' => null,
                'given_name' => null,
                'father_husband_name' => null,
                'full_name' => null,
                'passport_no' => 'DRAFT0001',
                'date_of_birth' => null,
                'birth_place' => null,
                'passport_expiry' => null,
                'address' => null,
                'mobile' => null,
                'cnic' => null,
                'blood_group' => null,
                'mehram_name' => null,
                'mehram_relation_id' => null,
                'waris_name' => null,
                'waris_cnic' => null,
                'waris_relation_id' => null,
                'waris_mobile' => null,
                'family_code' => null,
                'family_number' => null,
                'family_member_suffix' => null,
                'age' => null,
                'photo_path' => null,
            ],
        );

        $this->command?->info('Hajj demo data seeded successfully.');
        $this->command?->info('Companies: Deyar-e-Noor (DYN), Al-Haram Travels (AHT), Noor Hajj Services (NHS — no logo).');
        $this->command?->info('Sample family: DYN-01-A / DYN-01-B / DYN-01-C (Ahmed, Fatima, Ali Khan).');
        $this->command?->info('Login: superadmin@travel.com / Admin@12345');
    }

    /** @param  array<string, mixed>  $attributes */
    private function seedPilgrim(array $attributes, PilgrimService $service, Company $company): Pilgrim
    {
        $hajjYear = (int) $attributes['hajj_year'];
        $dob = Carbon::parse((string) $attributes['date_of_birth']);
        $suffix = strtoupper((string) $attributes['family_member_suffix']);
        $familyNumber = (int) $attributes['family_number'];

        $attributes['full_name'] = $service->buildFullName(
            (string) $attributes['surname'],
            (string) $attributes['given_name'],
        );
        $attributes['age'] = $service->calculateAge($dob, $hajjYear);
        $attributes['family_code'] = $service->formatFamilyCode($company, $familyNumber, $suffix);
        $attributes['family_member_suffix'] = $suffix;

        return Pilgrim::query()->updateOrCreate(
            [
                'hajj_year' => $hajjYear,
                'passport_no' => $attributes['passport_no'],
            ],
            $attributes,
        );
    }

    private function seedStorageImage(string $path): string
    {
        Storage::disk('public')->makeDirectory(dirname($path));

        if (! Storage::disk('public')->exists($path)) {
            Storage::disk('public')->put(
                $path,
                (string) file_get_contents(public_path('images/logo.png')),
            );
        }

        return $path;
    }
}
