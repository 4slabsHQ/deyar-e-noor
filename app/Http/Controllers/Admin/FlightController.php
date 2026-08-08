<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFlightRequest;
use App\Http\Requests\UpdateFlightRequest;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\City;
use App\Models\Flight;
use App\Services\FlightService;

class FlightController extends Controller
{
    public function index()
    {
        $flights = Flight::query()
            ->with([
                'departureCity',
                'departureAirport',
                'departureAirline',
                'arrivalCity',
                'arrivalAirport',
            ])
            ->latest()
            ->paginate(15);

        return view('admin.flights.index', compact('flights'));
    }

    public function create()
    {
        return view('admin.flights.create', $this->formOptions());
    }

    public function store(StoreFlightRequest $request, FlightService $flightService)
    {
        Flight::query()->create($request->flightPayload($flightService));

        return redirect()->route('admin.flights.index')->with('success', 'Flight saved successfully.');
    }

    public function edit(Flight $flight)
    {
        $flight->load(['departureAirline', 'viaAirline']);

        return view('admin.flights.edit', array_merge(
            ['flight' => $flight],
            $this->formOptions()
        ));
    }

    public function update(UpdateFlightRequest $request, Flight $flight, FlightService $flightService)
    {
        $flight->update($request->flightPayload($flightService));

        return redirect()->route('admin.flights.index')->with('success', 'Flight updated successfully.');
    }

    public function destroy(Flight $flight)
    {
        $flight->delete();

        return redirect()->route('admin.flights.index')->with('success', 'Flight deleted successfully.');
    }

    /** @return array<string, mixed> */
    private function formOptions(): array
    {
        $airlines = Airline::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn (Airline $airline) => [
                'id' => $airline->id,
                'name' => $airline->name,
                'code' => strtoupper((string) ($airline->iata_code ?: $airline->code)),
            ]);

        return [
            'cities' => City::query()->where('is_active', true)->orderBy('name')->get(),
            'airports' => Airport::query()->where('is_active', true)->orderBy('name')->get(),
            'airlines' => $airlines,
        ];
    }
}
