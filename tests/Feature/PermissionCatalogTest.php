<?php

use App\Models\Pilgrim;
use App\Models\User;
use App\Support\PermissionCatalog;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

test('permission catalog includes hajj registration and flights permissions', function () {
    $names = PermissionCatalog::allPermissionNames();

    expect($names)->toContain('pilgrims.view');
    expect($names)->toContain('pilgrims.create');
    expect($names)->toContain('flights.view');
    expect($names)->toContain('flights.create');
    expect($names)->toContain('companies.view');
});

test('database seeder creates only super admin role and user', function () {
    $this->seed(DatabaseSeeder::class);

    expect(Role::pluck('name')->all())->toBe(['Super Admin']);
    expect(User::count())->toBe(1);
    expect(User::first()?->hasRole('Super Admin'))->toBeTrue();
    expect(Permission::where('name', 'leads.view')->exists())->toBeFalse();
    expect(Pilgrim::count())->toBe(0);
});
