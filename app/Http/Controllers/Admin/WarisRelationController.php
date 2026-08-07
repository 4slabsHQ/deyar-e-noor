<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWarisRelationRequest;
use App\Http\Requests\UpdateWarisRelationRequest;
use App\Models\WarisRelation;

class WarisRelationController extends Controller
{
    public function index()
    {
        $warisRelations = WarisRelation::query()->orderBy('name')->paginate(15);

        return view('admin.waris-relations.index', compact('warisRelations'));
    }

    public function create()
    {
        return view('admin.waris-relations.create');
    }

    public function store(StoreWarisRelationRequest $request)
    {
        WarisRelation::create($request->validated());

        return redirect()->route('admin.waris-relations.index')->with('success', 'Waris relation created successfully.');
    }

    public function edit(WarisRelation $warisRelation)
    {
        return view('admin.waris-relations.edit', compact('warisRelation'));
    }

    public function update(UpdateWarisRelationRequest $request, WarisRelation $warisRelation)
    {
        $warisRelation->update($request->validated());

        return redirect()->route('admin.waris-relations.index')->with('success', 'Waris relation updated successfully.');
    }

    public function destroy(WarisRelation $warisRelation)
    {
        $warisRelation->delete();

        return redirect()->route('admin.waris-relations.index')->with('success', 'Waris relation deleted successfully.');
    }
}
