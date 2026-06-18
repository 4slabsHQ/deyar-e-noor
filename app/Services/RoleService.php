<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleService
{
    public function getAll()
    {
        return Role::with('permissions')->latest()->paginate(15);
    }

    public function create(array $data): Role
    {
        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        
        $permissions = Permission::whereIn('id', $data['permissions'] ?? [])->get();
        $role->syncPermissions($permissions);

        return $role;
    }

    public function update(Role $role, array $data): Role
    {
        $role->update(['name' => $data['name']]);

        $permissions = Permission::whereIn('id', $data['permissions'] ?? [])->get();
        $role->syncPermissions($permissions);

        return $role;
    }

    public function delete(Role $role): void
    {
        if ($role->name === 'Super Admin') {
            abort(403, 'Super Admin role cannot be deleted.');
        }

        $role->delete();
    }
}