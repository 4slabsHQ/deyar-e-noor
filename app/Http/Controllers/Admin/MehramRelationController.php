<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMehramRelationRequest;
use App\Http\Requests\UpdateMehramRelationRequest;
use App\Models\MehramRelation;

class MehramRelationController extends Controller
{
    public function index()
    {
        $mehramRelations = MehramRelation::query()->orderBy('name')->paginate(15);

        return view('admin.mehram-relations.index', compact('mehramRelations'));
    }

    public function create()
    {
        return view('admin.mehram-relations.create');
    }

    public function store(StoreMehramRelationRequest $request)
    {
        MehramRelation::create($request->validated());

        return redirect()->route('admin.mehram-relations.index')->with('success', 'Mehram relation created successfully.');
    }

    public function edit(MehramRelation $mehramRelation)
    {
        return view('admin.mehram-relations.edit', compact('mehramRelation'));
    }

    public function update(UpdateMehramRelationRequest $request, MehramRelation $mehramRelation)
    {
        $mehramRelation->update($request->validated());

        return redirect()->route('admin.mehram-relations.index')->with('success', 'Mehram relation updated successfully.');
    }

    public function destroy(MehramRelation $mehramRelation)
    {
        $mehramRelation->delete();

        return redirect()->route('admin.mehram-relations.index')->with('success', 'Mehram relation deleted successfully.');
    }
}
