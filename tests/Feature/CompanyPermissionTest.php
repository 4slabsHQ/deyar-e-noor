<?php

use App\Models\Company;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    Company::factory()->create(['name' => 'Test Co', 'code' => 'TST', 'is_active' => true]);
});

test('user with only companies.view permission can access companies index', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('companies.view');

    $this->actingAs($user)->get(route('admin.companies.index'))
        ->assertOk()
        ->assertSee('Test Co');
});

test('user with only companies.view permission cannot create companies', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('companies.view');

    $this->actingAs($user)->get(route('admin.companies.create'))
        ->assertForbidden();
});

test('user with companies.view via role can access companies index', function () {
    $role = Role::create(['name' => 'Company Viewer', 'guard_name' => 'web']);
    $role->givePermissionTo('companies.view');

    $user = User::factory()->create();
    $user->assignRole($role);

    $this->actingAs($user)->get(route('admin.companies.index'))
        ->assertOk()
        ->assertSee('Test Co');
});

test('user without companies.view gets forbidden on companies index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('admin.companies.index'))
        ->assertForbidden();
});
