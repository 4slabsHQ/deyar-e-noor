<?php

use App\Models\Pilgrim;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('registration staff lands on pilgrims list after login', function () {
    $user = User::factory()->create([
        'email' => 'registrar@deyar.com',
        'password' => bcrypt('Password@123'),
    ]);
    $user->assignRole('Registration Staff');

    $this->post(route('login'), [
        'email' => 'registrar@deyar.com',
        'password' => 'Password@123',
    ])->assertRedirect(route('dashboard'));
});

test('registration staff sidebar is minimal', function () {
    $user = User::factory()->create();
    $user->assignRole('Registration Staff');

    $this->actingAs($user)->get(route('dashboard'))
        ->assertOk()
        ->assertSee('nav-text">Dashboard', false)
        ->assertSee('nav-text">Hajj Registration', false)
        ->assertDontSee('nav-text">CRM', false)
        ->assertDontSee('nav-text">Organization', false);
});

test('dashboard shows pilgrim stats for users with pilgrims permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('pilgrims.view');

    Pilgrim::factory()->create(['full_name' => 'Ahmed Khan']);

    $this->actingAs($user)->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Total Registrations')
        ->assertSee('Recent Registrations')
        ->assertSee('Ahmed Khan');
});
