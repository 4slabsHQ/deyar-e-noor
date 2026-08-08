<?php

namespace App\Http\Requests;

use App\Services\FlightService;

class UpdateFlightRequest extends StoreFlightRequest
{
    /** @return array<string, mixed> */
    public function flightPayload(FlightService $flightService): array
    {
        $data = $flightService->prepareFlightData($this->validated());
        $data['updated_by'] = auth()->id();

        return $data;
    }
}
