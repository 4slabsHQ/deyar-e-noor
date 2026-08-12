<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Services\RoleService;
use App\Support\PermissionCatalog;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct(protected RoleService $service)
    {
        //
    }

    public function index()
    {
        $roles = $this->service->getAll();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = PermissionCatalog::activePermissions();

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(StoreRoleRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $permissions = PermissionCatalog::activePermissions();
        $role->load('permissions');

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $this->service->update($role, $request->validated());

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $this->service->delete($role);

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}
