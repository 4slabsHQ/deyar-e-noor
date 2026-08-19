<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Models\City;
use App\Models\Country;
use App\Models\Flight;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::query()
            ->with('country')
            ->orderBy('name')
            ->get();

        return view('admin.cities.index', compact('cities'));
    }

    public function create()
    {
        $countries = Country::orderBy('name')->get();

        return view('admin.cities.create', compact('countries'));
    }

    public function store(StoreCityRequest $request)
    {
        City::create($request->validated());

        return redirect()->route('admin.cities.index')->with('success', 'City created successfully.');
    }

    public function edit(City $city)
    {
        $countries = Country::orderBy('name')->get();

        return view('admin.cities.edit', compact('city', 'countries'));
    }

    public function update(UpdateCityRequest $request, City $city)
    {
        $city->update($request->validated());

        return redirect()->route('admin.cities.index')->with('success', 'City updated successfully.');
    }

    public function destroy(City $city)
    {
        if ($city->airports()->exists()) {
            return back()->with('error', 'Cannot delete a city that has airports linked to it.');
        }

        if ($city->pilgrims()->exists()) {
            return back()->with('error', 'Cannot delete a city used in pilgrim registrations.');
        }

        if (Flight::query()
            ->where('departure_city_id', $city->id)
            ->orWhere('via_city_id', $city->id)
            ->orWhere('arrival_city_id', $city->id)
            ->exists()) {
            return back()->with('error', 'Cannot delete a city that is linked to flights.');
        }

        $city->delete();

        return redirect()->route('admin.cities.index')->with('success', 'City deleted successfully.');
    }
}
