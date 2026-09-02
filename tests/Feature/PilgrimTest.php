<?php

use App\Enums\BloodGroup;
use App\Enums\FlightDirection;
use App\Enums\Gender;
use App\Enums\HajjSeasonStatus;
use App\Enums\PackageDuration;
use App\Models\CareOff;
use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\Flight;
use App\Models\FormOwner;
use App\Models\HajjSeason;
use App\Models\MaktabCategory;
use App\Models\MehramRelation;
use App\Models\Package;
use App\Models\Pilgrim;
use App\Models\PilgrimDeletionLog;
use App\Models\RoomType;
use App\Models\User;
use App\Models\WarisRelation;
use App\Services\HajjSeasonService;
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

    $this->hajjYear = app(HajjSeasonService::class)->activeYear();

    $this->country = Country::factory()->create(['iso2' => 'PK', 'name' => 'Pakistan']);
    $this->city = City::factory()->create(['country_id' => $this->country->id, 'name' => 'Lahore', 'is_active' => true]);
    $this->company = Company::factory()->create(['code' => 'DYN', 'name' => 'Deyar-e-Noor', 'is_active' => true, 'hajj_year' => $this->hajjYear]);
    $this->formOwner = FormOwner::factory()->create(['name' => 'Self', 'is_active' => true, 'hajj_year' => $this->hajjYear]);
    $this->maktabCategory = MaktabCategory::factory()->create(['name' => 'Category A', 'zone' => 'Zone 1', 'is_active' => true, 'hajj_year' => $this->hajjYear]);
    $this->package = Package::factory()->create([
        'number' => 'PKG-001',
        'name' => 'Economy',
        'price' => 850000,
        'days' => 21,
        'qurbani_included' => true,
        'duration' => PackageDuration::Long,
        'is_active' => true,
        'hajj_year' => $this->hajjYear,
    ]);
    $this->careOff = CareOff::factory()->create(['name' => 'Head Office', 'is_active' => true, 'hajj_year' => $this->hajjYear]);
    $this->roomType = RoomType::factory()->create(['name' => 'Sharing', 'is_active' => true, 'hajj_year' => $this->hajjYear]);
    $this->mehramRelation = MehramRelation::factory()->create(['name' => 'Husband', 'is_active' => true, 'hajj_year' => $this->hajjYear]);
    $this->warisRelation = WarisRelation::factory()->create(['name' => 'Son', 'is_active' => true, 'hajj_year' => $this->hajjYear]);
});

function validPilgrimPayload(array $overrides = []): array
{
    $passportNo = $overrides['passport_no'] ?? 'AB1234567';
    $passportDigits = preg_replace('/\D/', '', $passportNo) ?: '1234567';
    $cnicSerial = str_pad((string) ((int) substr($passportDigits, -7) % 10000000), 7, '0', STR_PAD_LEFT);

    return array_merge([
        'hajj_year' => (string) test()->hajjYear,
        'entry_date' => now()->toDateString(),
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
        'cnic' => '35201-'.$cnicSerial.'-1',
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
        ->and($pilgrim->age)->toBe($this->hajjYear - 1975)
        ->and($pilgrim->created_by)->toBe($this->user->id);
});

test('pilgrim index shows who entered the registration', function () {
    $pilgrim = registerPilgrim(['passport_no' => 'AB9999999']);

    $this->actingAs($this->user)->get(route('admin.pilgrims.index'))
        ->assertOk()
        ->assertSee('Entered By', false)
        ->assertSee($this->user->name, false)
        ->assertSee($pilgrim->passport_no, false);
});

test('admin can save optional comments on pilgrim registration', function () {
    $pilgrim = registerPilgrim([
        'comments' => 'Needs wheelchair assistance at airport.',
    ]);

    expect($pilgrim->comments)->toBe('Needs wheelchair assistance at airport.');

    $this->actingAs($this->user)->put(route('admin.pilgrims.update', $pilgrim), validPilgrimPayload([
        'passport_no' => $pilgrim->passport_no,
        'comments' => '',
    ]))->assertRedirect(route('admin.pilgrims.index'));

    expect($pilgrim->fresh()->comments)->toBeNull();
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

test('admin can register a pilgrim with all fields left empty', function () {
    $this->actingAs($this->user)->post(route('admin.pilgrims.store'), [
        'hajj_year' => '',
        'entry_date' => '',
    ])->assertRedirect(route('admin.pilgrims.index'));

    expect(Pilgrim::count())->toBe(1);
});

test('admin can delete an incomplete pilgrim registration', function () {
    $pilgrim = Pilgrim::query()->create([
        'hajj_year' => null,
        'entry_date' => null,
        'full_name' => null,
        'family_code' => null,
        'family_number' => null,
        'family_member_suffix' => null,
        'age' => null,
    ]);

    $this->actingAs($this->user)->delete(route('admin.pilgrims.destroy', $pilgrim))
        ->assertRedirect(route('admin.pilgrims.index'));

    expect(Pilgrim::count())->toBe(0);
});

test('admin can register a pilgrim without mehram or waris name and relation', function () {
    $pilgrim = registerPilgrim([
        'passport_no' => 'CD7654321',
        'mehram_name' => null,
        'mehram_relation_id' => null,
        'waris_name' => null,
        'waris_relation_id' => null,
    ]);

    expect($pilgrim->mehram_name)->toBeNull()
        ->and($pilgrim->mehram_relation_id)->toBeNull()
        ->and($pilgrim->waris_name)->toBeNull()
        ->and($pilgrim->waris_relation_id)->toBeNull();
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

    $this->actingAs($this->user)->get(route('admin.pilgrims.index'))
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

test('create registration form shows active hajj year and entry date as read-only', function () {
    $this->actingAs($this->user)->get(route('admin.pilgrims.create'))
        ->assertOk()
        ->assertSee('Entry Date')
        ->assertSee('Family & Association', false)
        ->assertSee('Documents', false)
        ->assertSee((string) $this->hajjYear)
        ->assertSee(now()->format('d/m/Y'))
        ->assertSee('Upload passport', false)
        ->assertSee('Upload visa', false)
        ->assertSee('Upload ticket', false);
});

test('registration uses active hajj season year regardless of submitted value', function () {
    $activeYear = $this->hajjYear + 2;

    HajjSeason::query()
        ->where('status', HajjSeasonStatus::Active)
        ->update(['status' => HajjSeasonStatus::Archived]);

    HajjSeason::query()->updateOrCreate(
        ['year' => $activeYear],
        ['status' => HajjSeasonStatus::Active, 'activated_at' => now()],
    );

    foreach ([Company::class, FormOwner::class, MaktabCategory::class, Package::class, CareOff::class, RoomType::class, MehramRelation::class, WarisRelation::class] as $model) {
        $model::query()->update(['hajj_year' => $activeYear]);
    }

    $pilgrim = registerPilgrim(['hajj_year' => (string) ($this->hajjYear + 5)]);

    expect($pilgrim->hajj_year)->toBe($activeYear);
});

test('registration sets entry date to today on create', function () {
    Carbon::setTestNow('2026-08-21 10:00:00');

    $pilgrim = registerPilgrim(['entry_date' => '2020-01-01']);

    expect($pilgrim->entry_date->toDateString())->toBe('2026-08-21');

    Carbon::setTestNow();
});

test('admin can upload passport visa and ticket on create', function () {
    Storage::fake('public');

    $this->actingAs($this->user)->post(route('admin.pilgrims.store'), array_merge(validPilgrimPayload(), [
        'passport' => UploadedFile::fake()->image('passport.jpg'),
        'visa' => UploadedFile::fake()->create('visa.pdf', 100, 'application/pdf'),
        'ticket' => UploadedFile::fake()->image('ticket.jpg'),
    ]))->assertRedirect(route('admin.pilgrims.index'));

    $pilgrim = Pilgrim::query()->where('passport_no', 'AB1234567')->firstOrFail();

    expect($pilgrim->passport_path)->not->toBeNull()
        ->and($pilgrim->visa_path)->not->toBeNull()
        ->and($pilgrim->ticket_path)->not->toBeNull()
        ->and($pilgrim->passport_url)->not->toBeNull()
        ->and($pilgrim->visa_url)->not->toBeNull()
        ->and($pilgrim->ticket_url)->not->toBeNull();

    Storage::disk('public')->assertExists($pilgrim->passport_path);
    Storage::disk('public')->assertExists($pilgrim->visa_path);
    Storage::disk('public')->assertExists($pilgrim->ticket_path);
});

test('qurbani defaults from package and can be overridden per pilgrim', function () {
    expect($this->package->qurbani_included)->toBeTrue();

    $pilgrim = registerPilgrim(['qurbani_included' => '1']);

    expect($pilgrim->qurbani_included)->toBeTrue();

    $pilgrimWithoutQurbani = registerPilgrim([
        'passport_no' => 'PQ7654321',
        'cnic' => '35201-5555555-5',
        'qurbani_included' => '0',
    ]);

    expect($pilgrimWithoutQurbani->qurbani_included)->toBeFalse()
        ->and($this->package->fresh()->qurbani_included)->toBeTrue();

    $packageWithoutQurbani = Package::create([
        'number' => 'PKG-002',
        'name' => 'Standard',
        'price' => 750000,
        'days' => 18,
        'qurbani_included' => false,
        'duration' => PackageDuration::Long,
        'is_active' => true,
    ]);

    $pilgrimOnNoQurbaniPackage = registerPilgrim([
        'passport_no' => 'RS1122334',
        'cnic' => '35201-6666666-6',
        'package_id' => $packageWithoutQurbani->id,
        'qurbani_included' => '0',
    ]);

    expect($pilgrimOnNoQurbaniPackage->qurbani_included)->toBeFalse()
        ->and($packageWithoutQurbani->fresh()->qurbani_included)->toBeFalse();
});

test('admin can update pilgrim qurbani without changing package settings', function () {
    registerPilgrim();

    $pilgrim = Pilgrim::query()->where('passport_no', 'AB1234567')->firstOrFail();

    $this->actingAs($this->user)->put(route('admin.pilgrims.update', $pilgrim), validPilgrimPayload([
        'passport_no' => $pilgrim->passport_no,
        'qurbani_included' => '0',
    ]))->assertRedirect(route('admin.pilgrims.index'));

    expect($pilgrim->fresh()->qurbani_included)->toBeFalse()
        ->and($this->package->fresh()->qurbani_included)->toBeTrue();
});

test('admin can remove pilgrim passport on update', function () {
    Storage::fake('public');

    $pilgrim = registerPilgrim();
    $path = 'pilgrims/test-passport.jpg';
    Storage::disk('public')->put($path, 'fake-image');
    $pilgrim->update(['passport_path' => $path]);

    $this->actingAs($this->user)->put(route('admin.pilgrims.update', $pilgrim), validPilgrimPayload([
        'passport_no' => $pilgrim->passport_no,
        'remove_passport' => '1',
    ]))->assertRedirect(route('admin.pilgrims.index'));

    expect($pilgrim->fresh()->passport_path)->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

test('pilgrim index page loads', function () {
    $this->actingAs($this->user)->get(route('admin.pilgrims.index'))
        ->assertOk()
        ->assertSee('Hajj Registration');
});

test('pilgrim index lists all registrations for datatables', function () {
    $pilgrims = Pilgrim::factory()->count(20)->create([
        'form_owner_id' => $this->formOwner->id,
        'company_id' => $this->company->id,
        'maktab_category_id' => $this->maktabCategory->id,
        'package_id' => $this->package->id,
        'care_off_id' => $this->careOff->id,
        'pod_city_id' => $this->city->id,
        'room_type_id' => $this->roomType->id,
        'mehram_relation_id' => $this->mehramRelation->id,
        'waris_relation_id' => $this->warisRelation->id,
        'hajj_year' => $this->hajjYear,
    ]);

    $response = $this->actingAs($this->user)->get(route('admin.pilgrims.index'))
        ->assertOk();

    foreach ($pilgrims as $pilgrim) {
        $response->assertSee($pilgrim->passport_no, false);
    }
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
    registerPilgrim(['passport_no' => 'AB1111111', 'given_name' => 'Member One']);
    registerPilgrim([
        'passport_no' => 'AB2222222',
        'existing_family_number' => 1,
        'given_name' => 'Member Two',
    ]);
    registerPilgrim([
        'passport_no' => 'AB3333333',
        'existing_family_number' => 1,
        'given_name' => 'Member Three',
    ]);
    registerPilgrim([
        'passport_no' => 'AB4444444',
        'existing_family_number' => 1,
        'given_name' => 'Member Four',
    ]);

    $response = $this->actingAs($this->user)->get(route('admin.pilgrims.families', [
        'company_id' => $this->company->id,
        'hajj_year' => $this->hajjYear,
    ]))->assertOk()
        ->assertJsonPath('families.0.family_number', 1)
        ->assertJsonPath('families.0.is_single', false)
        ->assertJsonPath('families.0.used_suffixes', ['A', 'B', 'C', 'D']);

    $label = $response->json('families.0.label');

    expect($label)
        ->toContain('A: Member One Khan')
        ->toContain('B: Member Two Khan')
        ->toContain('C: Member Three Khan')
        ->toContain('D: Member Four Khan');
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
    registerPilgrim(['passport_no' => 'AB1111111']);

    $otherYearPilgrim = Pilgrim::factory()->create([
        'passport_no' => 'AB2222222',
        'hajj_year' => $this->hajjYear + 1,
        'company_id' => $this->company->id,
        'form_owner_id' => $this->formOwner->id,
        'maktab_category_id' => $this->maktabCategory->id,
        'package_id' => $this->package->id,
        'care_off_id' => $this->careOff->id,
        'pod_city_id' => $this->city->id,
        'room_type_id' => $this->roomType->id,
        'mehram_relation_id' => $this->mehramRelation->id,
        'waris_relation_id' => $this->warisRelation->id,
        'family_code' => 'DYN-01-S',
        'family_number' => 1,
        'family_member_suffix' => 'S',
    ]);

    expect(Pilgrim::query()->where('passport_no', 'AB1111111')->first()->family_code)->toBe('DYN-01-S')
        ->and($otherYearPilgrim->family_code)->toBe('DYN-01-S');
});

test('same passport can exist in a different hajj year', function () {
    registerPilgrim(['passport_no' => 'AB1111111']);

    Pilgrim::factory()->create([
        'passport_no' => 'AB1111111',
        'hajj_year' => $this->hajjYear + 1,
        'company_id' => $this->company->id,
        'form_owner_id' => $this->formOwner->id,
        'maktab_category_id' => $this->maktabCategory->id,
        'package_id' => $this->package->id,
        'care_off_id' => $this->careOff->id,
        'pod_city_id' => $this->city->id,
        'room_type_id' => $this->roomType->id,
        'mehram_relation_id' => $this->mehramRelation->id,
        'waris_relation_id' => $this->warisRelation->id,
        'cnic' => '35201-9999999-9',
    ]);

    expect(Pilgrim::query()->where('passport_no', 'AB1111111')->count())->toBe(2);
});

test('duplicate passport is blocked within the same hajj year', function () {
    registerPilgrim(['passport_no' => 'AB1111111']);

    $this->actingAs($this->user)->post(route('admin.pilgrims.store'), validPilgrimPayload([
        'passport_no' => 'AB1111111',
        'cnic' => '35201-8888888-8',
    ]))
        ->assertSessionHasErrors('passport_no');
});

test('duplicate cnic is blocked within the same hajj year', function () {
    registerPilgrim(['passport_no' => 'AB1111111', 'cnic' => '35201-1234567-1']);

    $this->actingAs($this->user)->post(route('admin.pilgrims.store'), validPilgrimPayload([
        'passport_no' => 'AB2222222',
        'cnic' => '35201-1234567-1',
    ]))
        ->assertSessionHasErrors('cnic');
});

test('same cnic can exist in a different hajj year', function () {
    registerPilgrim(['passport_no' => 'AB1111111', 'cnic' => '35201-1234567-1']);

    Pilgrim::factory()->create([
        'passport_no' => 'AB9999999',
        'cnic' => '35201-1234567-1',
        'hajj_year' => $this->hajjYear + 1,
        'company_id' => $this->company->id,
        'form_owner_id' => $this->formOwner->id,
        'maktab_category_id' => $this->maktabCategory->id,
        'package_id' => $this->package->id,
        'care_off_id' => $this->careOff->id,
        'pod_city_id' => $this->city->id,
        'room_type_id' => $this->roomType->id,
        'mehram_relation_id' => $this->mehramRelation->id,
        'waris_relation_id' => $this->warisRelation->id,
    ]);

    expect(Pilgrim::query()->where('cnic', '35201-1234567-1')->count())->toBe(2);
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
        ->assertSee('DYN-01-S')
        ->assertDontSee('Family Member')
        ->assertSee('Print')
        ->assertSee('Save as PDF');
});

test('pilgrim registration document shows company logo when available', function () {
    Storage::fake('public');

    $logoPath = UploadedFile::fake()->image('company-logo.jpg')->store('companies/logos', 'public');

    $this->company->update(['logo' => $logoPath]);

    registerPilgrim();

    $pilgrim = Pilgrim::query()->where('passport_no', 'AB1234567')->firstOrFail();

    $this->actingAs($this->user)->get(route('admin.pilgrims.show', $pilgrim))
        ->assertOk()
        ->assertSee(Storage::url($logoPath), false)
        ->assertSee('pilgrim-doc-logo', false);
});

test('pilgrim registration document shows munazzam and package details section', function () {
    $this->company->update(['munazzam_code' => 'MZ-DYN-100']);

    registerPilgrim();

    $pilgrim = Pilgrim::query()->where('passport_no', 'AB1234567')->firstOrFail();

    $this->actingAs($this->user)->get(route('admin.pilgrims.show', $pilgrim))
        ->assertOk()
        ->assertSee('Munazzam')
        ->assertSee('MZ-DYN-100')
        ->assertSeeInOrder(['Package Details', 'Package No', 'Package Name', 'Price', 'Days', 'Duration', 'Maktab Category', 'Zone', 'Qurbani'], false)
        ->assertSee('PKG-001')
        ->assertSee('Economy')
        ->assertSee('850,000.00')
        ->assertSee('21')
        ->assertSee('Long')
        ->assertSee('Category A')
        ->assertSee('Zone 1')
        ->assertSee('Yes');
});

test('pilgrim edit form uses compact document upload controls', function () {
    Storage::fake('public');
    Storage::disk('public')->put('pilgrims/passports/uploaded-passport.pdf', 'passport-bytes');

    $pilgrim = registerPilgrim();
    $pilgrim->update(['passport_path' => 'pilgrims/passports/uploaded-passport.pdf']);

    $response = $this->actingAs($this->user)->get(route('admin.pilgrims.edit', $pilgrim));

    $response->assertOk()
        ->assertSee('admin-image-upload__remove', false)
        ->assertSee('fa-trash', false)
        ->assertSee('admin-image-upload__filename', false)
        ->assertDontSee('>Change</button>', false)
        ->assertDontSee('>Remove</button>', false);
});

test('pilgrim registration validates passport format', function () {
    $this->actingAs($this->user)->post(route('admin.pilgrims.store'), validPilgrimPayload([
        'passport_no' => 'INVALID',
    ]))->assertSessionHasErrors('passport_no');
});

test('pilgrim registration rejects date of birth with more than four digit year', function () {
    $this->actingAs($this->user)->post(route('admin.pilgrims.store'), validPilgrimPayload([
        'date_of_birth' => '202456-03-15',
    ]))->assertSessionHasErrors('date_of_birth');
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

test('pilgrim registration is blocked when company quota is reached', function () {
    $this->company->update(['quota' => 1]);

    registerPilgrim(['passport_no' => 'AB1111111']);

    $this->actingAs($this->user)->from(route('admin.pilgrims.create'))
        ->post(route('admin.pilgrims.store'), validPilgrimPayload([
            'passport_no' => 'AB2222222',
        ]))
        ->assertRedirect(route('admin.pilgrims.create'))
        ->assertSessionHasErrors('company_id')
        ->assertSessionHasErrors(['company_id' => 'Company quota reached for Deyar-e-Noor (Hajj '.$this->hajjYear.': 1/1).']);

    expect(Pilgrim::query()->count())->toBe(1);
});

test('pilgrim registration shows validation alert when quota or limit is reached', function () {
    $this->company->update(['quota' => 1]);
    $this->package->update(['limit' => 1]);

    registerPilgrim(['passport_no' => 'AB1111111']);

    $this->actingAs($this->user)->from(route('admin.pilgrims.create'))
        ->post(route('admin.pilgrims.store'), validPilgrimPayload([
            'passport_no' => 'AB2222222',
        ]))
        ->assertRedirect(route('admin.pilgrims.create'));

    $response = $this->actingAs($this->user)->get(route('admin.pilgrims.create'));

    $response->assertOk()
        ->assertSee('Could not save')
        ->assertSee('Company quota reached for Deyar-e-Noor')
        ->assertSee('Package limit reached for Economy');
});

test('pilgrim registration is blocked when package limit is reached', function () {
    $this->package->update(['limit' => 1]);

    registerPilgrim(['passport_no' => 'AB1111111']);

    $this->actingAs($this->user)->from(route('admin.pilgrims.create'))
        ->post(route('admin.pilgrims.store'), validPilgrimPayload([
            'passport_no' => 'AB2222222',
        ]))
        ->assertRedirect(route('admin.pilgrims.create'))
        ->assertSessionHasErrors('package_id');

    expect(Pilgrim::query()->count())->toBe(1);
});

test('pilgrim registration is blocked when form owner limit is reached', function () {
    $this->formOwner->update(['limit' => 1]);

    registerPilgrim(['passport_no' => 'AB1111111']);

    $this->actingAs($this->user)->from(route('admin.pilgrims.create'))
        ->post(route('admin.pilgrims.store'), validPilgrimPayload([
            'passport_no' => 'AB2222222',
        ]))
        ->assertRedirect(route('admin.pilgrims.create'))
        ->assertSessionHasErrors('form_owner_id');

    expect(Pilgrim::query()->count())->toBe(1);
});

test('pilgrim registration is allowed when company has no quota limit', function () {
    $this->company->update(['quota' => null]);

    registerPilgrim(['passport_no' => 'AB1111111']);
    registerPilgrim(['passport_no' => 'AB2222222']);

    expect(Pilgrim::query()->count())->toBe(2);
});

test('pilgrim update keeps registration when company quota is already full', function () {
    $this->company->update(['quota' => 1]);

    $pilgrim = registerPilgrim(['passport_no' => 'AB1111111', 'given_name' => 'Ahmed']);

    $this->actingAs($this->user)->put(route('admin.pilgrims.update', $pilgrim), validPilgrimPayload([
        'passport_no' => 'AB1111111',
        'given_name' => 'Ali',
    ]))->assertRedirect(route('admin.pilgrims.index'));

    expect($pilgrim->fresh()->given_name)->toBe('Ali');
});

test('pilgrim update keeps registration when package limit is already full', function () {
    $this->package->update(['limit' => 1]);

    $pilgrim = registerPilgrim(['passport_no' => 'AB1111111', 'given_name' => 'Ahmed']);

    $this->actingAs($this->user)->put(route('admin.pilgrims.update', $pilgrim), validPilgrimPayload([
        'passport_no' => 'AB1111111',
        'given_name' => 'Ali',
    ]))->assertRedirect(route('admin.pilgrims.index'));

    expect($pilgrim->fresh()->given_name)->toBe('Ali');
});

test('admin can transfer a pilgrim to another company as a new single family', function () {
    $otherCompany = Company::factory()->create(['code' => 'ABC', 'name' => 'Other Company', 'is_active' => true]);

    $pilgrim = registerPilgrim(['passport_no' => 'AB1111111', 'given_name' => 'Ahmed']);

    $this->actingAs($this->user)->put(route('admin.pilgrims.update', $pilgrim), validPilgrimPayload([
        'passport_no' => 'AB1111111',
        'company_id' => $otherCompany->id,
    ]))->assertRedirect(route('admin.pilgrims.index'));

    $pilgrim->refresh();

    expect($pilgrim->company_id)->toBe($otherCompany->id)
        ->and($pilgrim->family_code)->toBe('ABC-01-S')
        ->and($pilgrim->family_member_suffix)->toBe('S');
});

test('transferring a pilgrim rebalances the previous company family', function () {
    registerPilgrim(['passport_no' => 'AB1111111', 'given_name' => 'Member A']);
    registerPilgrim(['passport_no' => 'AB2222222', 'given_name' => 'Member B', 'existing_family_number' => 1]);

    $transfer = Pilgrim::query()->where('passport_no', 'AB2222222')->firstOrFail();
    $survivor = Pilgrim::query()->where('passport_no', 'AB1111111')->firstOrFail();
    $otherCompany = Company::factory()->create(['code' => 'ABC', 'name' => 'Other Company', 'is_active' => true]);

    $this->actingAs($this->user)->put(route('admin.pilgrims.update', $transfer), validPilgrimPayload([
        'passport_no' => 'AB2222222',
        'company_id' => $otherCompany->id,
    ]))->assertRedirect(route('admin.pilgrims.index'));

    expect($survivor->fresh()->family_code)->toBe('DYN-01-S')
        ->and($transfer->fresh()->family_code)->toBe('ABC-01-S');
});

test('admin can transfer a pilgrim into an existing family in another company', function () {
    $otherCompany = Company::factory()->create(['code' => 'ABC', 'name' => 'Other Company', 'is_active' => true]);

    $this->actingAs($this->user)->post(route('admin.pilgrims.store'), validPilgrimPayload([
        'passport_no' => 'AB9999999',
        'company_id' => $otherCompany->id,
    ]))->assertRedirect(route('admin.pilgrims.index'));

    $pilgrim = registerPilgrim(['passport_no' => 'AB1111111', 'given_name' => 'Transfer Me']);

    $this->actingAs($this->user)->put(route('admin.pilgrims.update', $pilgrim), validPilgrimPayload([
        'passport_no' => 'AB1111111',
        'company_id' => $otherCompany->id,
        'existing_family_number' => 1,
    ]))->assertRedirect(route('admin.pilgrims.index'));

    expect($pilgrim->fresh()->family_code)->toBe('ABC-01-B')
        ->and(Pilgrim::query()->where('passport_no', 'AB9999999')->first()->family_code)->toBe('ABC-01-A');
});

test('family code preview returns a new code when company is changed on edit', function () {
    $otherCompany = Company::factory()->create(['code' => 'ABC', 'name' => 'Other Company', 'is_active' => true]);
    $pilgrim = registerPilgrim(['passport_no' => 'AB1111111']);

    $this->actingAs($this->user)->get(route('admin.pilgrims.preview-family-code', [
        'company_id' => $otherCompany->id,
        'hajj_year' => $this->hajjYear,
        'pilgrim_id' => $pilgrim->id,
    ]))
        ->assertOk()
        ->assertJson([
            'family_code' => 'ABC-01-S',
            'suffix' => 'S',
            'promote_single' => false,
        ]);
});

test('admin can move a pilgrim to another family within the same company', function () {
    registerPilgrim(['passport_no' => 'AB1111111', 'given_name' => 'Member A']);
    registerPilgrim(['passport_no' => 'AB2222222', 'given_name' => 'Member B', 'existing_family_number' => 1]);
    registerPilgrim(['passport_no' => 'AB3333333', 'given_name' => 'Member C']);

    $memberB = Pilgrim::query()->where('passport_no', 'AB2222222')->firstOrFail();

    $this->actingAs($this->user)->put(route('admin.pilgrims.update', $memberB), validPilgrimPayload([
        'passport_no' => 'AB2222222',
        'family_move_to' => '2',
    ]))->assertRedirect(route('admin.pilgrims.index'));

    expect($memberB->fresh()->family_code)->toBe('DYN-02-B')
        ->and(Pilgrim::query()->where('passport_no', 'AB1111111')->first()->family_code)->toBe('DYN-01-S')
        ->and(Pilgrim::query()->where('passport_no', 'AB3333333')->first()->family_code)->toBe('DYN-02-A');
});

test('admin can move a pilgrim to a new single family within the same company', function () {
    registerPilgrim(['passport_no' => 'AB1111111', 'given_name' => 'Member A']);
    $memberB = registerPilgrim(['passport_no' => 'AB2222222', 'given_name' => 'Member B', 'existing_family_number' => 1]);

    $this->actingAs($this->user)->put(route('admin.pilgrims.update', $memberB), validPilgrimPayload([
        'passport_no' => 'AB2222222',
        'family_move_to' => 'new',
    ]))->assertRedirect(route('admin.pilgrims.index'));

    expect($memberB->fresh()->family_member_suffix)->toBe('S')
        ->and(Pilgrim::query()->where('passport_no', 'AB1111111')->first()->family_code)->toBe('DYN-01-S');
});

test('keeping family assignment preserves the current family on edit', function () {
    $pilgrim = registerPilgrim(['passport_no' => 'AB1111111']);

    $this->actingAs($this->user)->put(route('admin.pilgrims.update', $pilgrim), validPilgrimPayload([
        'passport_no' => 'AB1111111',
        'given_name' => 'Updated',
        'family_move_to' => 'keep',
    ]))->assertRedirect(route('admin.pilgrims.index'));

    expect($pilgrim->fresh()->given_name)->toBe('Updated')
        ->and($pilgrim->fresh()->family_code)->toBe('DYN-01-S');
});

test('deletion preview shows registration details family impact and flights', function () {
    $flight = Flight::factory()->create([
        'direction' => FlightDirection::Outbound,
        'departure_flight_no' => 'DELPREVIEW1',
    ]);

    registerPilgrim(['passport_no' => 'AB1111111', 'given_name' => 'Member A']);
    registerPilgrim(['passport_no' => 'AB2222222', 'given_name' => 'Member B', 'existing_family_number' => 1]);
    registerPilgrim(['passport_no' => 'AB3333333', 'given_name' => 'Member C', 'existing_family_number' => 1]);

    $memberB = Pilgrim::query()->where('passport_no', 'AB2222222')->firstOrFail();
    $flight->pilgrims()->attach($memberB->id, ['assigned_by' => $this->user->id]);

    $response = $this->actingAs($this->user)
        ->getJson(route('admin.pilgrims.deletion-preview', $memberB))
        ->assertOk();

    expect($response->json('pilgrim.full_name'))->toContain('Member B')
        ->and($response->json('family.outcome'))->toBe('rebalance')
        ->and($response->json('family.other_members'))->toHaveCount(2)
        ->and(collect($response->json('family.changes'))->pluck('new_family_code')->all())
        ->toBe(['DYN-01-A', 'DYN-01-B'])
        ->and($response->json('flights.0.flight_no'))->toBe('DELPREVIEW1');
});

test('pilgrims index includes delete preview modal assets', function () {
    registerPilgrim(['passport_no' => 'AB1111111']);

    $this->actingAs($this->user)
        ->get(route('admin.pilgrims.index'))
        ->assertOk()
        ->assertSee('pilgrim-delete-modal', false)
        ->assertSee('pilgrim-delete.js', false)
        ->assertSee(route('admin.pilgrims.deletion-preview', Pilgrim::query()->first()), false);
});

test('deleting a pilgrim removes flight assignments', function () {
    $flight = Flight::factory()->create([
        'direction' => FlightDirection::Outbound,
    ]);

    $pilgrim = registerPilgrim(['passport_no' => 'AB1111111']);
    $flight->pilgrims()->attach($pilgrim->id, ['assigned_by' => $this->user->id]);

    $this->actingAs($this->user)
        ->delete(route('admin.pilgrims.destroy', $pilgrim))
        ->assertRedirect(route('admin.pilgrims.index'));

    expect($flight->fresh()->pilgrims()->count())->toBe(0);
});

test('deletion preview denies users without delete permission', function () {
    $pilgrim = registerPilgrim(['passport_no' => 'AB1111111']);
    $viewer = User::factory()->create();
    $viewer->givePermissionTo('pilgrims.view');

    $this->actingAs($viewer)
        ->getJson(route('admin.pilgrims.deletion-preview', $pilgrim))
        ->assertForbidden();
});

test('deleting a pilgrim records an audit log entry', function () {
    $pilgrim = registerPilgrim([
        'passport_no' => 'AB9999999',
        'given_name' => 'Audit',
        'surname' => 'Delete',
    ]);

    $this->actingAs($this->user)
        ->delete(route('admin.pilgrims.destroy', $pilgrim))
        ->assertRedirect(route('admin.pilgrims.index'));

    $log = PilgrimDeletionLog::query()->first();

    expect($log)->not->toBeNull()
        ->and($log->pilgrim_id)->toBe($pilgrim->id)
        ->and($log->deleted_by)->toBe($this->user->id)
        ->and($log->full_name)->toContain('Audit')
        ->and($log->passport_no)->toBe('AB9999999')
        ->and($log->family_code)->toBe('DYN-01-S');
});
