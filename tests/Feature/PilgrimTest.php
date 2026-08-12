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
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $this->user = User::factory()->create();
    $this->user->assignRole('Super Admin');

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
    $this->hajjYear = (int) now()->year;
});

function validPilgrimPayload(array $overrides = []): array
{
    return array_merge([
        'hajj_year' => (string) test()->hajjYear,
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

function registerPilgrim(array $overrides = []): Pilgrim
{
    test()->actingAs(test()->user)->post(route('admin.pilgrims.store'), validPilgrimPayload($overrides))
        ->assertRedirect(route('admin.pilgrims.index'));

    return Pilgrim::query()->where('passport_no', $overrides['passport_no'] ?? 'AB1234567')->firstOrFail();
}

test('admin can register a pilgrim as single with S', function () {
    registerPilgrim();

    $pilgrim = Pilgrim::query()->where('passport_no', 'AB1234567')->first();

    expect($pilgrim)->not->toBeNull()
        ->and($pilgrim->full_name)->toBe('Ahmed Khan')
        ->and($pilgrim->family_code)->toBe('DYN-01-S')
        ->and($pilgrim->age)->toBe($this->hajjYear - 1975);
});

test('admin can update a pilgrim', function () {
    registerPilgrim();

    $pilgrim = Pilgrim::query()->where('passport_no', 'AB1234567')->firstOrFail();

    $this->actingAs($this->user)->put(route('admin.pilgrims.update', $pilgrim), validPilgrimPayload([
        'given_name' => 'Ali',
        'passport_no' => 'AB1234567',
    ]))->assertRedirect(route('admin.pilgrims.index'));

    expect($pilgrim->fresh()->full_name)->toBe('Ali Khan')
        ->and($pilgrim->fresh()->family_code)->toBe('DYN-01-S');
});

test('admin can upload pilgrim photo on create and view registration', function () {
    Storage::fake('public');

    $this->actingAs($this->user)->post(route('admin.pilgrims.store'), array_merge(validPilgrimPayload(), [
        'photo' => UploadedFile::fake()->image('pilgrim.jpg'),
    ]))->assertRedirect(route('admin.pilgrims.index'));

    $pilgrim = Pilgrim::query()->where('passport_no', 'AB1234567')->firstOrFail();

    expect($pilgrim->photo_path)->not->toBeNull()
        ->and($pilgrim->photo_url)->not->toBeNull();

    Storage::disk('public')->assertExists($pilgrim->photo_path);

    $this->actingAs($this->user)->get(route('admin.pilgrims.show', $pilgrim))
        ->assertOk()
        ->assertSee($pilgrim->photo_url, false);
});

test('admin can remove pilgrim photo on update', function () {
    Storage::fake('public');

    $pilgrim = registerPilgrim();
    $path = 'pilgrims/test-photo.jpg';
    Storage::disk('public')->put($path, 'fake-image');
    $pilgrim->update(['photo_path' => $path]);

    $this->actingAs($this->user)->put(route('admin.pilgrims.update', $pilgrim), validPilgrimPayload([
        'passport_no' => $pilgrim->passport_no,
        'remove_photo' => '1',
    ]))->assertRedirect(route('admin.pilgrims.index'));

    expect($pilgrim->fresh()->photo_path)->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

test('pilgrim index page loads', function () {
    $this->actingAs($this->user)->get(route('admin.pilgrims.index'))
        ->assertOk()
        ->assertSee('Hajj Registration');
});

test('pilgrim family code preview returns single code', function () {
    $this->actingAs($this->user)->get(route('admin.pilgrims.preview-family-code', [
        'company_id' => $this->company->id,
        'hajj_year' => $this->hajjYear,
    ]))->assertOk()
        ->assertJson(['family_code' => 'DYN-01-S', 'suffix' => 'S', 'promote_single' => false]);
});

test('adding member to single promotes existing to A and assigns B', function () {
    $single = registerPilgrim(['passport_no' => 'AB1111111']);

    $this->actingAs($this->user)->get(route('admin.pilgrims.preview-family-code', [
        'company_id' => $this->company->id,
        'hajj_year' => $this->hajjYear,
        'family_number' => 1,
    ]))->assertOk()
        ->assertJson([
            'family_code' => 'DYN-01-B',
            'suffix' => 'B',
            'promote_single' => true,
        ]);

    registerPilgrim([
        'passport_no' => 'AB2222222',
        'existing_family_number' => 1,
    ]);

    expect($single->fresh()->family_code)->toBe('DYN-01-A')
        ->and(Pilgrim::query()->where('passport_no', 'AB2222222')->first()->family_code)->toBe('DYN-01-B');
});

test('admin can list existing families for a company and hajj year', function () {
    registerPilgrim(['passport_no' => 'AB1111111']);
    registerPilgrim([
        'passport_no' => 'AB2222222',
        'existing_family_number' => 1,
    ]);

    $this->actingAs($this->user)->get(route('admin.pilgrims.families', [
        'company_id' => $this->company->id,
        'hajj_year' => $this->hajjYear,
    ]))->assertOk()
        ->assertJsonPath('families.0.family_number', 1)
        ->assertJsonPath('families.0.is_single', false)
        ->assertJsonPath('families.0.used_suffixes', ['A', 'B']);
});

test('admin can add a third member to an existing family', function () {
    registerPilgrim(['passport_no' => 'AB1111111']);
    registerPilgrim([
        'passport_no' => 'AB2222222',
        'existing_family_number' => 1,
    ]);
    registerPilgrim([
        'passport_no' => 'AB3333333',
        'existing_family_number' => 1,
    ]);

    expect(Pilgrim::query()->where('family_code', 'DYN-01-C')->exists())->toBeTrue();
});

test('deleting one of two family members reverts survivor to S', function () {
    $single = registerPilgrim(['passport_no' => 'AB1111111']);
    registerPilgrim([
        'passport_no' => 'AB2222222',
        'existing_family_number' => 1,
    ]);

    $memberB = Pilgrim::query()->where('passport_no', 'AB2222222')->firstOrFail();

    $this->actingAs($this->user)->delete(route('admin.pilgrims.destroy', $memberB))
        ->assertRedirect(route('admin.pilgrims.index'));

    expect($single->fresh()->family_code)->toBe('DYN-01-S')
        ->and(Pilgrim::query()->where('family_number', 1)->count())->toBe(1);
});

test('deleting a middle member rebalances remaining suffixes', function () {
    registerPilgrim(['passport_no' => 'AB1111111', 'given_name' => 'Member A']);
    registerPilgrim(['passport_no' => 'AB2222222', 'existing_family_number' => 1, 'given_name' => 'Member B']);
    registerPilgrim(['passport_no' => 'AB3333333', 'existing_family_number' => 1, 'given_name' => 'Member C']);
    registerPilgrim(['passport_no' => 'AB4444444', 'existing_family_number' => 1, 'given_name' => 'Member D']);
    registerPilgrim(['passport_no' => 'AB5555555', 'existing_family_number' => 1, 'given_name' => 'Member E']);

    $memberC = Pilgrim::query()->where('passport_no', 'AB3333333')->firstOrFail();

    $this->actingAs($this->user)->delete(route('admin.pilgrims.destroy', $memberC))
        ->assertRedirect(route('admin.pilgrims.index'));

    expect(Pilgrim::query()->where('family_number', 1)->orderBy('family_member_suffix')->pluck('family_code')->all())
        ->toBe(['DYN-01-A', 'DYN-01-B', 'DYN-01-C', 'DYN-01-D']);
});

test('deleting the only member frees the family number for reuse', function () {
    registerPilgrim(['passport_no' => 'AB1111111']);
    registerPilgrim(['passport_no' => 'AB2222222']);

    $first = Pilgrim::query()->where('passport_no', 'AB1111111')->firstOrFail();

    $this->actingAs($this->user)->delete(route('admin.pilgrims.destroy', $first))
        ->assertRedirect(route('admin.pilgrims.index'));

    $replacement = registerPilgrim(['passport_no' => 'AB3333333']);

    expect($replacement->family_code)->toBe('DYN-01-S')
        ->and(Pilgrim::query()->where('passport_no', 'AB2222222')->first()->family_code)->toBe('DYN-02-S');
});

test('family numbers reset per hajj year', function () {
    registerPilgrim(['passport_no' => 'AB1111111', 'hajj_year' => (string) $this->hajjYear]);
    registerPilgrim(['passport_no' => 'AB2222222', 'hajj_year' => (string) ($this->hajjYear + 1)]);

    expect(Pilgrim::query()->where('passport_no', 'AB1111111')->first()->family_code)->toBe('DYN-01-S')
        ->and(Pilgrim::query()->where('passport_no', 'AB2222222')->first()->family_code)->toBe('DYN-01-S');
});

test('same passport can register in a different hajj year', function () {
    registerPilgrim(['passport_no' => 'AB1111111', 'hajj_year' => (string) $this->hajjYear]);
    registerPilgrim(['passport_no' => 'AB1111111', 'hajj_year' => (string) ($this->hajjYear + 1), 'cnic' => '35201-9999999-9']);

    expect(Pilgrim::query()->where('passport_no', 'AB1111111')->count())->toBe(2);
});

test('admin can view pilgrim registration document', function () {
    registerPilgrim();

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
    $company = Company::factory()->create(['code' => 'XYZ']);

    $family = $service->prepareNewSingleFamily($company, 2026);

    expect($family['family_code'])->toBe('XYZ-01-S')
        ->and($service->buildFullName('Khan', 'Ahmed'))->toBe('Ahmed Khan')
        ->and($service->calculateAge(Carbon::parse('1975-03-15'), 2026))->toBe(51);
});
