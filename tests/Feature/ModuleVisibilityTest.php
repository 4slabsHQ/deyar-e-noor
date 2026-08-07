<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['modules.show_legacy_travel_erp' => false]);
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('sidebar hides legacy travel erp modules by default', function () {
    $user = User::factory()->create();
    $user->assignRole('Admin');

    $this->actingAs($user)->get(route('dashboard'))
        ->assertOk()
        ->assertSee('nav-text">Hajj Registration', false)
        ->assertSee('nav-text">Hajj Masters', false)
        ->assertDontSee('nav-text">CRM', false)
        ->assertDontSee('nav-text">Organization', false)
        ->assertDontSee('nav-text">Parties', false)
        ->assertDontSee('nav-text">Master Data', false);
});

test('sidebar shows legacy travel erp modules when enabled', function () {
    config(['modules.show_legacy_travel_erp' => true]);

    $user = User::factory()->create();
    $user->assignRole('Admin');

    $this->actingAs($user)->get(route('dashboard'))
        ->assertOk()
        ->assertSee('nav-text">CRM', false)
        ->assertSee('nav-text">Master Data', false);
});
