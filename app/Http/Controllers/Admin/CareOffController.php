<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCareOffRequest;
use App\Http\Requests\UpdateCareOffRequest;
use App\Models\CareOff;

class CareOffController extends Controller
{
    public function index()
    {
        $careOffs = CareOff::query()->orderBy('name')->paginate(15);

        return view('admin.care-offs.index', compact('careOffs'));
    }

    public function create()
    {
        return view('admin.care-offs.create');
    }

    public function store(StoreCareOffRequest $request)
    {
        CareOff::create($request->validated());

        return redirect()->route('admin.care-offs.index')->with('success', 'Care off created successfully.');
    }

    public function edit(CareOff $careOff)
    {
        return view('admin.care-offs.edit', compact('careOff'));
    }

    public function update(UpdateCareOffRequest $request, CareOff $careOff)
    {
        $careOff->update($request->validated());

        return redirect()->route('admin.care-offs.index')->with('success', 'Care off updated successfully.');
    }

    public function destroy(CareOff $careOff)
    {
        $careOff->delete();

        return redirect()->route('admin.care-offs.index')->with('success', 'Care off deleted successfully.');
    }
}
