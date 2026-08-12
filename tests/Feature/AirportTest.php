<?php

use App\Models\Airport;
use App\Models\City;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $this->user = User::factory()->create();
    $this->user->assignRole('Super Admin');

    $this->city = City::factory()->create(['name' => 'Lahore']);
});

test('admin can view airports index', function () {
    $response = $this->actingAs($this->user)->get(route('admin.airports.index'));

    $response->assertOk();
});

test('admin can create an airport', function () {
    $response = $this->actingAs($this->user)->post(route('admin.airports.store'), [
        'name' => 'Allama Iqbal International Airport',
        'code' => 'lhe',
        'city_id' => $this->city->id,
        'is_active' => '1',
    ]);

    $response->assertRedirect(route('admin.airports.index'));

    $airport = Airport::query()->first();

    expect($airport)->not->toBeNull()
        ->and($airport->code)->toBe('LHE')
        ->and($airport->city_id)->toBe($this->city->id);
});

test('admin can update an airport', function () {
    $airport = Airport::factory()->create([
        'city_id' => $this->city->id,
        'name' => 'Old Name',
        'code' => 'OLD',
    ]);

    $response = $this->actingAs($this->user)->put(route('admin.airports.update', $airport), [
        'name' => 'Updated Airport',
        'code' => 'NEW',
        'city_id' => $this->city->id,
        'is_active' => '1',
    ]);

    $response->assertRedirect(route('admin.airports.index'));

    expect($airport->fresh()->name)->toBe('Updated Airport')
        ->and($airport->fresh()->code)->toBe('NEW');
});

test('airport code must be unique within the same city', function () {
    Airport::factory()->create([
        'city_id' => $this->city->id,
        'code' => 'LHE',
    ]);

    $response = $this->actingAs($this->user)->from(route('admin.airports.create'))
        ->post(route('admin.airports.store'), [
            'name' => 'Duplicate Code Airport',
            'code' => 'LHE',
            'city_id' => $this->city->id,
            'is_active' => '1',
        ]);

    $response->assertRedirect(route('admin.airports.create'));
    $response->assertSessionHasErrors('code');
});
