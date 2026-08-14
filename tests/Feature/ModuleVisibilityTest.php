<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('sidebar shows hajj masters and access control for super admin', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    $this->actingAs($user)->get(route('dashboard'))
        ->assertOk()
        ->assertSee('nav-text">Hajj Masters', false)
        ->assertSee('nav-text">Access Control', false)
        ->assertSee('nav-text">Hajj Registration', false)
        ->assertSee('nav-text">Flights', false);
});

test('sidebar shows hajj registration for users with pilgrim permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('pilgrims.view');

    $this->actingAs($user)->get(route('dashboard'))
        ->assertOk()
        ->assertSee('nav-text">Hajj Registration', false);
});

test('sidebar hides hajj registration for users without pilgrim permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('companies.view');

    $this->actingAs($user)->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('nav-text">Hajj Registration', false);
});

test('only super admin role exists after seeding', function () {
    expect(Role::pluck('name')->all())->toBe(['Super Admin']);
});
