<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFormOwnerRequest;
use App\Http\Requests\UpdateFormOwnerRequest;
use App\Models\FormOwner;

class FormOwnerController extends Controller
{
    public function index()
    {
        $formOwners = FormOwner::query()->orderBy('name')->paginate(15);

        return view('admin.form-owners.index', compact('formOwners'));
    }

    public function create()
    {
        return view('admin.form-owners.create');
    }

    public function store(StoreFormOwnerRequest $request)
    {
        FormOwner::create($request->validated());

        return redirect()->route('admin.form-owners.index')->with('success', 'Form owner created successfully.');
    }

    public function edit(FormOwner $formOwner)
    {
        return view('admin.form-owners.edit', compact('formOwner'));
    }

    public function update(UpdateFormOwnerRequest $request, FormOwner $formOwner)
    {
        $formOwner->update($request->validated());

        return redirect()->route('admin.form-owners.index')->with('success', 'Form owner updated successfully.');
    }

    public function destroy(FormOwner $formOwner)
    {
        $formOwner->delete();

        return redirect()->route('admin.form-owners.index')->with('success', 'Form owner deleted successfully.');
    }
}
