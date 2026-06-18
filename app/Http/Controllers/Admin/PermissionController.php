<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{

    public function index()
    {
        $permissions = Permission::orderBy('name')->paginate(20);

        return view('admin.permissions.index', compact('permissions'));
    }
}