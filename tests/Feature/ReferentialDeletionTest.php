<?php

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
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $this->user = User::factory()->create();
    $this->user->assignRole('Super Admin');
});

function createLinkedPilgrimMasterData(): array
{
    $country = Country::factory()->create();
    $city = City::factory()->create(['country_id' => $country->id]);
    $company = Company::factory()->create(['code' => 'DYN', 'is_active' => true]);
    $formOwner = FormOwner::create(['name' => 'Self', 'is_active' => true]);
    $maktabCategory = MaktabCategory::create(['name' => 'Category A', 'zone' => 'Zone 1', 'is_active' => true]);
    $package = Package::create([
        'number' => 'PKG-001',
        'name' => 'Economy',
        'price' => 850000,
        'days' => 21,
        'qurbani_included' => true,
        'duration' => PackageDuration::Long,
        'is_active' => true,
    ]);
    $careOff = CareOff::create(['name' => 'Head Office', 'is_active' => true]);
    $roomType = RoomType::create(['name' => 'Sharing', 'is_active' => true]);
    $mehramRelation = MehramRelation::create(['name' => 'Husband', 'is_active' => true]);
    $warisRelation = WarisRelation::create(['name' => 'Son', 'is_active' => true]);

    return compact(
        'country',
        'city',
        'company',
        'formOwner',
        'maktabCategory',
        'package',
        'careOff',
        'roomType',
        'mehramRelation',
        'warisRelation',
    );
}

test('admin cannot delete a package linked to hajj registrations', function () {
    $masters = createLinkedPilgrimMasterData();

    Pilgrim::factory()->create([
        'company_id' => $masters['company']->id,
        'form_owner_id' => $masters['formOwner']->id,
        'maktab_category_id' => $masters['maktabCategory']->id,
        'package_id' => $masters['package']->id,
        'care_off_id' => $masters['careOff']->id,
        'pod_city_id' => $masters['city']->id,
        'room_type_id' => $masters['roomType']->id,
        'mehram_relation_id' => $masters['mehramRelation']->id,
        'waris_relation_id' => $masters['warisRelation']->id,
        'created_by' => $this->user->id,
    ]);

    $this->actingAs($this->user)->from(route('admin.packages.index'))
        ->delete(route('admin.packages.destroy', $masters['package']))
        ->assertRedirect(route('admin.packages.index'))
        ->assertSessionHas('error');

    expect(Package::withTrashed()->find($masters['package']->id))->not->toBeNull();
});

test('admin can delete an unused package', function () {
    $package = Package::create([
        'number' => 'PKG-999',
        'name' => 'Unused',
        'price' => 100000,
        'days' => 14,
        'qurbani_included' => false,
        'duration' => PackageDuration::Short,
        'is_active' => true,
    ]);

    $this->actingAs($this->user)->delete(route('admin.packages.destroy', $package))
        ->assertRedirect(route('admin.packages.index'))
        ->assertSessionHas('success');

    expect(Package::query()->find($package->id))->toBeNull()
        ->and(Package::withTrashed()->find($package->id))->not->toBeNull();
});

test('admin cannot delete a company linked to hajj registrations', function () {
    $masters = createLinkedPilgrimMasterData();

    Pilgrim::factory()->create([
        'company_id' => $masters['company']->id,
        'form_owner_id' => $masters['formOwner']->id,
        'maktab_category_id' => $masters['maktabCategory']->id,
        'package_id' => $masters['package']->id,
        'care_off_id' => $masters['careOff']->id,
        'pod_city_id' => $masters['city']->id,
        'room_type_id' => $masters['roomType']->id,
        'mehram_relation_id' => $masters['mehramRelation']->id,
        'waris_relation_id' => $masters['warisRelation']->id,
        'created_by' => $this->user->id,
    ]);

    $this->actingAs($this->user)->from(route('admin.companies.index'))
        ->delete(route('admin.companies.destroy', $masters['company']))
        ->assertRedirect(route('admin.companies.index'))
        ->assertSessionHas('error');

    expect(Company::withTrashed()->find($masters['company']->id))->not->toBeNull();
});

test('admin cannot delete a form owner linked to hajj registrations', function () {
    $masters = createLinkedPilgrimMasterData();

    Pilgrim::factory()->create([
        'company_id' => $masters['company']->id,
        'form_owner_id' => $masters['formOwner']->id,
        'maktab_category_id' => $masters['maktabCategory']->id,
        'package_id' => $masters['package']->id,
        'care_off_id' => $masters['careOff']->id,
        'pod_city_id' => $masters['city']->id,
        'room_type_id' => $masters['roomType']->id,
        'mehram_relation_id' => $masters['mehramRelation']->id,
        'waris_relation_id' => $masters['warisRelation']->id,
        'created_by' => $this->user->id,
    ]);

    $this->actingAs($this->user)->from(route('admin.form-owners.index'))
        ->delete(route('admin.form-owners.destroy', $masters['formOwner']))
        ->assertRedirect(route('admin.form-owners.index'))
        ->assertSessionHas('error');

    expect(FormOwner::withTrashed()->find($masters['formOwner']->id))->not->toBeNull();
});

test('admin cannot delete a country that has cities', function () {
    $country = Country::factory()->create();
    City::factory()->create(['country_id' => $country->id]);

    $this->actingAs($this->user)->from(route('admin.countries.index'))
        ->delete(route('admin.countries.destroy', $country))
        ->assertRedirect(route('admin.countries.index'))
        ->assertSessionHas('error');

    expect(Country::query()->find($country->id))->not->toBeNull();
});

test('admin cannot delete a user linked to hajj registrations they entered', function () {
    $masters = createLinkedPilgrimMasterData();
    $staff = User::factory()->create();

    Pilgrim::factory()->create([
        'company_id' => $masters['company']->id,
        'form_owner_id' => $masters['formOwner']->id,
        'maktab_category_id' => $masters['maktabCategory']->id,
        'package_id' => $masters['package']->id,
        'care_off_id' => $masters['careOff']->id,
        'pod_city_id' => $masters['city']->id,
        'room_type_id' => $masters['roomType']->id,
        'mehram_relation_id' => $masters['mehramRelation']->id,
        'waris_relation_id' => $masters['warisRelation']->id,
        'created_by' => $staff->id,
    ]);

    $this->actingAs($this->user)->from(route('admin.users.index'))
        ->delete(route('admin.users.destroy', $staff))
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('error');

    expect(User::query()->find($staff->id))->not->toBeNull();
});

test('database restricts hard deleting a package linked to pilgrims', function () {
    $masters = createLinkedPilgrimMasterData();

    Pilgrim::factory()->create([
        'company_id' => $masters['company']->id,
        'form_owner_id' => $masters['formOwner']->id,
        'maktab_category_id' => $masters['maktabCategory']->id,
        'package_id' => $masters['package']->id,
        'care_off_id' => $masters['careOff']->id,
        'pod_city_id' => $masters['city']->id,
        'room_type_id' => $masters['roomType']->id,
        'mehram_relation_id' => $masters['mehramRelation']->id,
        'waris_relation_id' => $masters['warisRelation']->id,
        'created_by' => $this->user->id,
    ]);

    expect(fn () => Package::query()->whereKey($masters['package']->id)->forceDelete())
        ->toThrow(QueryException::class);
});
