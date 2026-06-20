<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHotelRequest;
use App\Http\Requests\UpdateHotelRequest;
use App\Models\City;
use App\Models\Country;
use App\Models\Hotel;

class HotelController extends Controller
{
    public function index()
    {
        $hotels = Hotel::with(['country', 'city'])->orderBy('name')->paginate(15);

        return view('admin.hotels.index', compact('hotels'));
    }

    public function create()
    {
        $countries = Country::orderBy('name')->get();
        $cities    = City::orderBy('name')->get();

        return view('admin.hotels.create', compact('countries', 'cities'));
    }

    public function store(StoreHotelRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        Hotel::create($data);

        return redirect()->route('admin.hotels.index')->with('success', 'Hotel created successfully.');
    }

    public function edit(Hotel $hotel)
    {
        $countries = Country::orderBy('name')->get();
        $cities    = City::orderBy('name')->get();

        return view('admin.hotels.edit', compact('hotel', 'countries', 'cities'));
    }

    public function update(UpdateHotelRequest $request, Hotel $hotel)
    {
        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        $hotel->update($data);

        return redirect()->route('admin.hotels.index')->with('success', 'Hotel updated successfully.');
    }

    public function destroy(Hotel $hotel)
    {
        $hotel->delete();

        return redirect()->route('admin.hotels.index')->with('success', 'Hotel deleted successfully.');
    }
}