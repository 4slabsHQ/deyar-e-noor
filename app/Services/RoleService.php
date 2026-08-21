<?php

namespace App\Services;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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

    public function delete(Role $role): ?string
    {
        if ($role->name === 'Super Admin') {
            return 'Super Admin role cannot be deleted.';
        }

        if ($role->users()->exists()) {
            return sprintf(
                'Cannot delete this role because it is assigned to %d user(s).',
                $role->users()->count(),
            );
        }

        $role->delete();

        return null;
    }
}
