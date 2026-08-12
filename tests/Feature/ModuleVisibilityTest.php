<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    config([
        'features.hajj_registration' => true,
        'features.flights' => true,
    ]);
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('sidebar shows hajj masters and access control for super admin', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    $this->actingAs($user)->get(route('dashboard'))
        ->assertOk()
        ->assertSee('nav-text">Hajj Masters', false)
        ->assertSee('nav-text">Access Control', false)
        ->assertSee('nav-text">Hajj Registration', false);
});

test('sidebar hides hajj registration when feature flag is disabled', function () {
    config(['features.hajj_registration' => false]);

    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    $this->actingAs($user)->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('nav-text">Hajj Registration', false);
});

test('only super admin role exists after seeding', function () {
    expect(Role::pluck('name')->all())->toBe(['Super Admin']);
});
