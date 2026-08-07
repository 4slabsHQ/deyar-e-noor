<?php

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
use App\Models\User;
use App\Models\WarisRelation;
use App\Services\PilgrimService;
use Carbon\Carbon;
use Database\Seeders\CountriesSeeder;
use Database\Seeders\HajjDemoDataSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $this->user = User::factory()->create();
    $this->user->assignRole('Admin');

    $this->country = Country::factory()->create(['iso2' => 'PK', 'name' => 'Pakistan']);
    $this->city = City::factory()->create(['country_id' => $this->country->id, 'name' => 'Lahore', 'is_active' => true]);
    $this->company = Company::factory()->create(['code' => 'DYN', 'name' => 'Deyar-e-Noor', 'is_active' => true]);
    $this->formOwner = FormOwner::create(['name' => 'Self', 'is_active' => true]);
    $this->maktabCategory = MaktabCategory::create(['name' => 'Category A', 'zone' => 'Zone 1', 'is_active' => true]);
    $this->package = Package::create([
        'number' => 'PKG-001',
        'name' => 'Economy',
        'price' => 850000,
        'days' => 21,
        'qurbani_included' => true,
        'duration' => PackageDuration::Long,
        'is_active' => true,
    ]);
    $this->careOff = CareOff::create(['name' => 'Head Office', 'is_active' => true]);
    $this->roomType = RoomType::create(['name' => 'Sharing', 'is_active' => true]);
    $this->mehramRelation = MehramRelation::create(['name' => 'Husband', 'is_active' => true]);
    $this->warisRelation = WarisRelation::create(['name' => 'Son', 'is_active' => true]);
});

function validPilgrimPayload(array $overrides = []): array
{
    return array_merge([
        'hajj_year' => (string) now()->year,
        'booking_date' => now()->toDateString(),
        'form_owner_id' => test()->formOwner->id,
        'company_id' => test()->company->id,
        'maktab_category_id' => test()->maktabCategory->id,
        'package_id' => test()->package->id,
        'care_off_id' => test()->careOff->id,
        'pod_city_id' => test()->city->id,
        'room_type_id' => test()->roomType->id,
        'gender' => Gender::Male->value,
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
        'blood_group' => BloodGroup::OPositive->value,
        'mehram_name' => 'Fatima Khan',
        'mehram_relation_id' => test()->mehramRelation->id,
        'waris_name' => 'Ali Khan',
        'waris_cnic' => '35201-7654321-3',
        'waris_relation_id' => test()->warisRelation->id,
        'waris_mobile' => '0300-9876543',
    ], $overrides);
}

test('admin can register a pilgrim as single with S', function () {
    $this->actingAs($this->user)->post(route('admin.pilgrims.store'), validPilgrimPayload())
        ->assertRedirect(route('admin.pilgrims.index'));

    $pilgrim = Pilgrim::query()->where('passport_no', 'AB1234567')->first();

    expect($pilgrim)->not->toBeNull()
        ->and($pilgrim->full_name)->toBe('Ahmed Khan')
        ->and($pilgrim->family_code)->toBe('DYN-1-S')
        ->and($pilgrim->age)->toBe(now()->year - 1975);
});

test('admin can update a pilgrim', function () {
    $this->actingAs($this->user)->post(route('admin.pilgrims.store'), validPilgrimPayload());

    $pilgrim = Pilgrim::query()->where('passport_no', 'AB1234567')->firstOrFail();

    $this->actingAs($this->user)->put(route('admin.pilgrims.update', $pilgrim), validPilgrimPayload([
        'given_name' => 'Ali',
        'passport_no' => 'AB1234567',
    ]))->assertRedirect(route('admin.pilgrims.index'));

    expect($pilgrim->fresh()->full_name)->toBe('Ali Khan')
        ->and($pilgrim->fresh()->family_code)->toBe('DYN-1-S');
});

test('pilgrim index page loads', function () {
    $this->actingAs($this->user)->get(route('admin.pilgrims.index'))
        ->assertOk()
        ->assertSee('Hajj Registration');
});

test('pilgrim family code preview returns single code', function () {
    $this->actingAs($this->user)->get(route('admin.pilgrims.preview-family-code', [
        'company_id' => $this->company->id,
    ]))->assertOk()
        ->assertJson(['family_code' => 'DYN-1-S', 'suffix' => 'S', 'promote_single' => false]);
});

test('adding member to single promotes existing to A and assigns B', function () {
    $this->actingAs($this->user)->post(route('admin.pilgrims.store'), validPilgrimPayload([
        'passport_no' => 'AB1111111',
    ]));

    $single = Pilgrim::query()->where('passport_no', 'AB1111111')->firstOrFail();

    $this->actingAs($this->user)->get(route('admin.pilgrims.preview-family-code', [
        'company_id' => $this->company->id,
        'family_number' => 1,
    ]))->assertOk()
        ->assertJson([
            'family_code' => 'DYN-1-B',
            'suffix' => 'B',
            'promote_single' => true,
        ]);

    $this->actingAs($this->user)->post(route('admin.pilgrims.store'), validPilgrimPayload([
        'passport_no' => 'AB2222222',
        'existing_family_number' => 1,
    ]))->assertRedirect(route('admin.pilgrims.index'));

    expect($single->fresh()->family_code)->toBe('DYN-1-A')
        ->and(Pilgrim::query()->where('passport_no', 'AB2222222')->first()->family_code)->toBe('DYN-1-B');
});

test('admin can list existing families for a company', function () {
    $this->actingAs($this->user)->post(route('admin.pilgrims.store'), validPilgrimPayload([
        'passport_no' => 'AB1111111',
    ]));

    $this->actingAs($this->user)->post(route('admin.pilgrims.store'), validPilgrimPayload([
        'passport_no' => 'AB2222222',
        'existing_family_number' => 1,
    ]));

    $this->actingAs($this->user)->get(route('admin.pilgrims.families', [
        'company_id' => $this->company->id,
    ]))->assertOk()
        ->assertJsonPath('families.0.family_number', 1)
        ->assertJsonPath('families.0.is_single', false)
        ->assertJsonPath('families.0.used_suffixes', ['A', 'B']);
});

test('admin can add a third member to an existing family', function () {
    $this->actingAs($this->user)->post(route('admin.pilgrims.store'), validPilgrimPayload([
        'passport_no' => 'AB1111111',
    ]));

    $this->actingAs($this->user)->post(route('admin.pilgrims.store'), validPilgrimPayload([
        'passport_no' => 'AB2222222',
        'existing_family_number' => 1,
    ]));

    $this->actingAs($this->user)->post(route('admin.pilgrims.store'), validPilgrimPayload([
        'passport_no' => 'AB3333333',
        'existing_family_number' => 1,
    ]))->assertRedirect(route('admin.pilgrims.index'));

    expect(Pilgrim::query()->where('family_code', 'DYN-1-C')->exists())->toBeTrue();
});

test('admin can view pilgrim registration document', function () {
    $this->actingAs($this->user)->post(route('admin.pilgrims.store'), validPilgrimPayload());

    $pilgrim = Pilgrim::query()->where('passport_no', 'AB1234567')->firstOrFail();

    $this->actingAs($this->user)->get(route('admin.pilgrims.show', $pilgrim))
        ->assertOk()
        ->assertSee('Hajj Registration Form')
        ->assertSee('Ahmed Khan')
        ->assertSee('AB1234567')
        ->assertSee('35201-1234567-1')
        ->assertSee('Print')
        ->assertSee('Save as PDF');
});

test('pilgrim registration validates passport format', function () {
    $this->actingAs($this->user)->post(route('admin.pilgrims.store'), validPilgrimPayload([
        'passport_no' => 'INVALID',
    ]))->assertSessionHasErrors('passport_no');
});

test('pilgrim registration normalizes cnic without dashes', function () {
    $this->actingAs($this->user)->post(route('admin.pilgrims.store'), validPilgrimPayload([
        'cnic' => '3520112345671',
        'waris_cnic' => '3520176543213',
    ]))->assertRedirect(route('admin.pilgrims.index'));

    $pilgrim = Pilgrim::query()->where('passport_no', 'AB1234567')->first();

    expect($pilgrim)->not->toBeNull()
        ->and($pilgrim->cnic)->toBe('35201-1234567-1')
        ->and($pilgrim->waris_cnic)->toBe('35201-7654321-3');
});

test('pilgrim service builds family code and age', function () {
    $service = app(PilgrimService::class);
    $company = Company::factory()->create(['code' => 'DYN']);

    $family = $service->prepareNewSingleFamily($company);

    expect($family['family_code'])->toBe('DYN-1-S')
        ->and($service->buildFullName('Khan', 'Ahmed'))->toBe('Ahmed Khan')
        ->and($service->calculateAge(Carbon::parse('1975-03-15'), 2026))->toBe(51);
});

test('hajj demo seeder creates master data and pilgrims', function () {
    $this->seed(CountriesSeeder::class);

    $this->seed(HajjDemoDataSeeder::class);

    expect(Company::query()->where('code', 'DYN')->exists())->toBeTrue()
        ->and(FormOwner::query()->count())->toBeGreaterThan(0)
        ->and(Package::query()->count())->toBeGreaterThan(0)
        ->and(Pilgrim::query()->count())->toBe(3);
});
