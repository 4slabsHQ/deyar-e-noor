<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAirportRequest;
use App\Http\Requests\UpdateAirportRequest;
use App\Models\Airport;
use App\Models\City;

class AirportController extends Controller
{
    public function index()
    {
        $airports = Airport::with('city')
            ->orderBy('name')
            ->get();

        return view('admin.airports.index', compact('airports'));
    }

    public function create()
    {
        $cities = City::query()->where('is_active', true)->orderBy('name')->get();

        return view('admin.airports.create', compact('cities'));
    }

    public function store(StoreAirportRequest $request)
    {
        $data = $request->validated();
        $data['code'] = strtoupper($data['code']);
        $data['created_by'] = auth()->id();

        Airport::create($data);

        return redirect()
            ->route('admin.airports.index')
            ->with('success', 'Airport created successfully.');
    }

    public function edit(Airport $airport)
    {
        $cities = City::query()->where('is_active', true)->orderBy('name')->get();

        return view('admin.airports.edit', compact('airport', 'cities'));
    }

    public function update(UpdateAirportRequest $request, Airport $airport)
    {
        $data = $request->validated();
        $data['code'] = strtoupper($data['code']);
        $data['updated_by'] = auth()->id();

        $airport->update($data);

        return redirect()
            ->route('admin.airports.index')
            ->with('success', 'Airport updated successfully.');
    }

    public function destroy(Airport $airport)
    {
        $airport->delete();

        return redirect()
            ->route('admin.airports.index')
            ->with('success', 'Airport deleted successfully.');
    }
}
