<?php

use App\Models\Airport;
use App\Models\City;
use App\Models\Country;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $this->user = User::factory()->create();
    $this->user->assignRole('Super Admin');
});

test('admin can create a city', function () {
    $country = Country::factory()->create();

    $response = $this->actingAs($this->user)->post(route('admin.cities.store'), [
        'name' => 'Karachi',
        'country_id' => $country->id,
        'is_active' => '1',
    ]);

    $response->assertRedirect(route('admin.cities.index'));

    expect(City::query()->first())
        ->name->toBe('Karachi')
        ->country_id->toBe($country->id)
        ->is_active->toBeTrue();
});

test('admin can update a city', function () {
    $country = Country::factory()->create();
    $city = City::factory()->create([
        'country_id' => $country->id,
        'name' => 'Lahore',
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)->put(route('admin.cities.update', $city), [
        'name' => 'Lahore City',
        'country_id' => $country->id,
        'is_active' => '0',
    ]);

    $response->assertRedirect(route('admin.cities.index'));

    expect($city->fresh())
        ->name->toBe('Lahore City')
        ->is_active->toBeFalse();
});

test('admin can delete an unused city', function () {
    $city = City::factory()->create();

    $response = $this->actingAs($this->user)->delete(route('admin.cities.destroy', $city));

    $response->assertRedirect(route('admin.cities.index'));
    expect(City::query()->find($city->id))->toBeNull();
});

test('admin cannot delete a city linked to an airport', function () {
    $city = City::factory()->create();

    Airport::factory()->create([
        'city_id' => $city->id,
    ]);

    $response = $this->actingAs($this->user)->from(route('admin.cities.index'))
        ->delete(route('admin.cities.destroy', $city));

    $response->assertRedirect(route('admin.cities.index'));
    $response->assertSessionHas('error');
    expect(City::query()->find($city->id))->not->toBeNull();
});
