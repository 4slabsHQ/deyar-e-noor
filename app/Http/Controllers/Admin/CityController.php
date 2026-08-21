<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Models\City;
use App\Models\Country;

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
        return $this->deleteOrBack(
            $city,
            'admin.cities.index',
            'City deleted successfully.',
        );
    }
}
