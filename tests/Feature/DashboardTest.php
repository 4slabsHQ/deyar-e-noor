<?php

use App\Models\Company;
use App\Models\Pilgrim;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('guests are redirected to the login page', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('dashboard shows quota metrics for users with company access', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    $company = Company::factory()->create([
        'name' => 'Deyar-e-Noor',
        'code' => 'DYN',
        'quota' => 100,
        'is_active' => true,
    ]);

    Pilgrim::query()->create([
        'company_id' => $company->id,
        'hajj_year' => now()->year,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Quota Overview')
        ->assertSee('Overall utilisation')
        ->assertSee('1%')
        ->assertSee('Company Quota Utilisation')
        ->assertSee('deyar-quota-progress__fill')
        ->assertSee('Recent Registrations')
        ->assertSee('View all')
        ->assertSee('Deyar-e-Noor')
        ->assertDontSee('Signed in as');
});

test('dashboard hides pilgrim widgets without pilgrim permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('companies.view');

    Company::factory()->create(['quota' => 50, 'is_active' => true]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Quota Overview')
        ->assertDontSee('Recent Registrations')
        ->assertDontSee('Registrations (Last 6 Months)');
});
