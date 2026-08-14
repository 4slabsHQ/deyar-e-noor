<?php

namespace App\Services;

use App\Support\PermissionCatalog;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSyncService
{
    public function sync(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (PermissionCatalog::allPermissionNames() as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        Permission::query()
            ->whereNotIn('name', PermissionCatalog::allPermissionNames())
            ->each(function (Permission $permission): void {
                $permission->roles()->detach();
                $permission->users()->detach();
                $permission->delete();
            });

        $superAdmin = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]);

        $superAdmin->syncPermissions(
            Permission::query()
                ->whereIn('name', PermissionCatalog::allPermissionNames())
                ->get()
        );

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
