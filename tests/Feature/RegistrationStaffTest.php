<?php

use App\Models\Pilgrim;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('user with pilgrim permissions lands on dashboard after login', function () {
    $user = User::factory()->create([
        'email' => 'registrar@deyar.com',
        'password' => bcrypt('Password@123'),
    ]);
    $user->givePermissionTo(['pilgrims.view', 'pilgrims.create', 'pilgrims.update']);

    $this->post(route('login'), [
        'email' => 'registrar@deyar.com',
        'password' => 'Password@123',
    ])->assertRedirect(route('dashboard'));
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
