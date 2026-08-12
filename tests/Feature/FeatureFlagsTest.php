<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    config([
        'features.hajj_registration' => true,
        'features.flights' => true,
    ]);
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('sidebar hides hajj registration when feature is disabled', function () {
    config([
        'features.hajj_registration' => false,
        'features.flights' => false,
    ]);

    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    $this->actingAs($user)->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('nav-text">Hajj Registration', false);
});

test('sidebar hides flights when feature is disabled', function () {
    config([
        'features.flights' => false,
        'features.hajj_registration' => false,
    ]);

    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    $this->actingAs($user)->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('nav-text">Flights', false);
});

test('pilgrim routes return 404 when hajj registration feature is disabled', function () {
    config(['features.hajj_registration' => false]);

    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    $this->actingAs($user)->get(route('admin.pilgrims.index'))
        ->assertNotFound();
});

test('flight routes return 404 when flights feature is disabled', function () {
    config(['features.flights' => false]);

    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    $this->actingAs($user)->get(route('admin.flights.index'))
        ->assertNotFound();
});

test('database seeder creates a single super admin user', function () {
    $this->seed(DatabaseSeeder::class);

    expect(User::where('email', 'superadmin@travel.com')->exists())->toBeTrue();
    expect(User::count())->toBe(1);
    expect(User::first()?->hasRole('Super Admin'))->toBeTrue();
});
