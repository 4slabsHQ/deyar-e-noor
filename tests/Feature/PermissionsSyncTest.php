<?php

use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('permissions sync creates missing permissions without deleting custom roles', function () {
    $adminRole = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
    $adminRole->givePermissionTo('companies.view');

    Permission::query()->where('name', 'like', 'pilgrims.%')->delete();
    Permission::query()->where('name', 'like', 'flights.%')->delete();

    $this->artisan('permissions:sync')->assertSuccessful();

    expect(Permission::where('name', 'pilgrims.view')->exists())->toBeTrue();
    expect(Permission::where('name', 'flights.view')->exists())->toBeTrue();
    expect(Role::where('name', 'Admin')->exists())->toBeTrue();
    expect($adminRole->fresh()->permissions->pluck('name'))->toContain('companies.view');
});
