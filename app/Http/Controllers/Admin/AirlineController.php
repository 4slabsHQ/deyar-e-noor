<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAirlineRequest;
use App\Http\Requests\UpdateAirlineRequest;
use App\Models\Airline;
use App\Models\Country;
use Illuminate\Support\Facades\Storage;

class AirlineController extends Controller
{
    public function index()
    {
        $airlines = Airline::with('country')->orderBy('name')->get();

        return view('admin.airlines.index', compact('airlines'));
    }

    public function create()
    {
        $countries = Country::orderBy('name')->get();

        return view('admin.airlines.create', compact('countries'));
    }

    public function store(StoreAirlineRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('airlines', 'public');
        }

        $data['created_by'] = auth()->id();

        Airline::create($data);

        return redirect()->route('admin.airlines.index')->with('success', 'Airline created successfully.');
    }

    public function edit(Airline $airline)
    {
        $countries = Country::orderBy('name')->get();

        return view('admin.airlines.edit', compact('airline', 'countries'));
    }

    public function update(UpdateAirlineRequest $request, Airline $airline)
    {
        $data = $request->validated();

        if ($request->boolean('remove_logo')) {
            if ($airline->logo) {
                Storage::disk('public')->delete($airline->logo);
            }
            $data['logo'] = null;
        }

        if ($request->hasFile('logo')) {
            if ($airline->logo) {
                Storage::disk('public')->delete($airline->logo);
            }
            $data['logo'] = $request->file('logo')->store('airlines', 'public');
        }

        unset($data['remove_logo']);

        $data['updated_by'] = auth()->id();

        $airline->update($data);

        return redirect()->route('admin.airlines.index')->with('success', 'Airline updated successfully.');
    }

    public function destroy(Airline $airline)
    {
        $airline->delete();

        return redirect()->route('admin.airlines.index')->with('success', 'Airline deleted successfully.');
    }
}
