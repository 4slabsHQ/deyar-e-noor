<?php

namespace Database\Seeders;

use App\Support\PermissionCatalog;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (PermissionCatalog::activePermissionNames() as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        Permission::query()
            ->whereNotIn('name', PermissionCatalog::activePermissionNames())
            ->each(function (Permission $permission): void {
                $permission->roles()->detach();
                $permission->users()->detach();
                $permission->delete();
            });

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(
            Permission::whereIn('name', PermissionCatalog::activePermissionNames())->get()
        );

        Role::query()
            ->where('name', '!=', 'Super Admin')
            ->each(function (Role $role): void {
                $role->users()->detach();
                $role->permissions()->detach();
                $role->delete();
            });

        $this->command?->info('Roles and permissions seeded successfully.');
    }
}
